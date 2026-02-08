<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyOption;
use App\Models\SurveyAnswer;
use App\Models\SurveyResponse;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class SurveyController extends Controller
{
    function __construct()
    {
        $this->middleware('role:survey-list', ['only' => ['index', 'show']]);
        $this->middleware('role:survey-create', ['only' => ['create', 'store']]);
        $this->middleware('role:survey-edit', ['only' => ['edit', 'update']]);
        $this->middleware('role:survey-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $config['title'] = "Survey";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Survey"],
        ];
        if ($request->ajax()) {
            $data = Survey::get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a class="btn btn-success" href="' . route('survey.edit', $row->id) . '"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-danger btn-delete" href="#" data-id ="' . $row->id . '" ><i class="fas fa-trash"></i></a>
                       <a class="btn btn-info" href="' . route('survey.show', $row->id) . '"><i class="fas fa-eye"></i></a>';
                    return $actionBtn;
                })->make();
        }
        return view('backend.survey.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $config['title'] = "Tambah Survey";
        $config['breadcrumbs'] = [
            ['url' => route('survey.index'), 'title' => "Survey"],
            ['url' => '#', 'title' => "Tambah Survey"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('survey.store')
        ];
        return view('backend.survey.form', compact('config'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'layanan_id' => 'required',
        ]);


        if ($validator->passes()) {

            DB::beginTransaction();
            try {

                // 1️⃣ SIMPAN SURVEY
                $slug = Str::slug($request->title) . '-' . $request->layanan_id;

                $survey = Survey::create([
                    'title'       => $request->title,
                    'slug'        => $slug,
                    'description' => $request->description,
                    'layanan_id'  => $request->layanan_id,
                ]);


                // 2️⃣ DECODE QUESTIONS (INI KUNCI UTAMA 🔥)
                $questions = json_decode($request->questions, true);

                if (!is_array($questions)) {
                    throw new \Exception('Format pertanyaan tidak valid');
                }

                // 3️⃣ SIMPAN QUESTIONS
                foreach ($questions as $i => $q) {

                    $question = SurveyQuestion::create([
                        'survey_id'  => $survey->id,
                        'type'       => $q['type'],
                        'question'   => $q['question'],
                        'is_required' => $q['required'] ?? false,
                        'order'      => $i
                    ]);

                    // 4️⃣ SIMPAN OPTIONS (RADIO / CHECKBOX)
                    if (in_array($q['type'], ['radio', 'checkbox']) && !empty($q['options'])) {
                        foreach ($q['options'] as $opt) {
                            SurveyOption::create([
                                'question_id' => $question->id,
                                'option_text' => $opt
                            ]);
                        }
                    }
                }

                DB::commit();
                $response = response()->json(['status' => 'success', 'message' => 'Berhasil Di Simpan', 'redirect' => route('survey.index')]);
            } catch (\Throwable $throw) {
                DB::rollBack();
                Log::error($throw);
                $response = response()->json(['status' => 'error', 'message' => $throw->getMessage()]);
            }
        } else {
            $response = response()->json(['status' => 'error', 'message' => $validator->errors()->all()]);
        }
        return $response;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $config['title'] = "Tambah Survey";
        // ambil survey + questions + active options
        $survey = Survey::with(['questions' => function ($q) {
            $q->orderBy('order');
        }, 'questions.options' => function ($opt) {
            $opt->where('is_active', 1); // hanya option aktif
        }])->findOrFail($id);

        return view('backend.survey.show', compact('survey', 'config'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $config['title'] = "Edit Survey";
        $config['breadcrumbs'] = [
            ['url' => route('survey.index'), 'title' => "Survey"],
            ['url' => '#', 'title' => "Edit Survey"],
        ];
        $data = Survey::with(['layanan', 'questions.options'])->findOrFail($id);

        $questions = $data->questions->map(function ($q) {
            return [
                'id' => $q->id,
                'type' => $q->type,
                'question' => $q->question,
                'required' => (bool) $q->is_required,
                'options' => in_array($q->type, ['radio', 'checkbox'])
                    ? $q->options->map(function ($opt) {
                        return [
                            'id' => $opt->id,
                            'text' => $opt->option_text,
                            'is_active' => $opt->is_active
                        ];
                    })->toArray()
                    : null
            ];
        });

        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('survey.update', $id)
        ];
        return view('backend.survey.form', compact('config', 'data', 'questions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);


        if ($validator->passes()) {

            DB::beginTransaction();
            try {


                // 1️⃣ UPDATE SURVEY
                $survey = Survey::findOrFail($id);
                $survey->update($request->only('title', 'description', 'is_active', 'layanan_id'));

                /**
                 * 2️⃣ NORMALISASI QUESTIONS
                 * Bisa:
                 * - array
                 * - json string
                 */
                $questions = $request->questions;

                if (is_string($questions)) {
                    $questions = json_decode($questions, true);
                }

                if (!is_array($questions)) {
                    throw new \Exception('Questions harus berupa array');
                }

                $keepQuestionIds = [];

                // 3️⃣ LOOP QUESTIONS
                foreach ($questions as $index => $q) {

                    // 🔥 JIKA QUESTION STRING → ERROR
                    if (!is_array($q)) {
                        continue; // skip data rusak
                    }

                    $question = SurveyQuestion::updateOrCreate(
                        ['id' => $q['id'] ?? null],
                        [
                            'survey_id'   => $survey->id,
                            'type'        => $q['type'] ?? 'text',
                            'question'    => $q['question'] ?? '',
                            'is_required' => $q['required'] ?? false,
                            'order'       => $index
                        ]
                    );

                    $keepQuestionIds[] = $question->id;

                    /**
                     * 4️⃣ NORMALISASI OPTIONS
                     */
                    if (in_array($question->type, ['radio', 'checkbox'])) {

                        $options = $q['options'] ?? [];

                        // options bisa string json
                        if (is_string($options)) {
                            $options = json_decode($options, true);
                        }

                        if (!is_array($options)) {
                            $options = [];
                        }

                        $keepOptionIds = [];

                        foreach ($options as $opt) {

                            // 🔥 OPTION STRING
                            if (is_string($opt)) {
                                $option = SurveyOption::create([
                                    'question_id' => $question->id,
                                    'option_text' => $opt
                                ]);
                            }
                            // 🔥 OPTION ARRAY
                            elseif (is_array($opt)) {
                                $option = SurveyOption::updateOrCreate(
                                    ['id' => $opt['id'] ?? null],
                                    [
                                        'question_id' => $question->id,
                                        'option_text' => $opt['text'] ?? $opt['option_text'] ?? ''
                                    ]
                                );
                            } else {
                                continue;
                            }

                            $keepOptionIds[] = $option->id;
                        }

                        // 🧹 HAPUS OPTION YANG DIHAPUS DI UI
                        SurveyOption::where('question_id', $question->id)
                            ->whereNotIn('id', $keepOptionIds)
                            ->update(['is_active' => 0]);
                    } else {
                        // text / textarea → hapus option
                        $question->options()->delete();
                    }
                }

                // 🧹 HAPUS QUESTION YANG DIHAPUS DI UI
                SurveyQuestion::where('survey_id', $survey->id)
                    ->whereNotIn('id', $keepQuestionIds)
                    ->delete();

                DB::commit();
                $response = response()->json(['status' => 'success', 'message' => 'Berhasil Di Update', 'redirect' => route('survey.index')]);
            } catch (\Throwable $throw) {
                DB::rollBack();
                Log::error($throw);
                $response = response()->json(['status' => 'error', 'message' => $throw->getMessage()]);
            }
        } else {
            $response = response()->json(['status' => 'error', 'message' => $validator->errors()->all()]);
        }
        return $response;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Normalisasi options agar selalu array
     */
    private function normalizeOptions($options): array
    {
        // Jika null
        if (empty($options)) {
            return [];
        }

        // Jika sudah array
        if (is_array($options)) {
            return array_values(array_filter($options));
        }

        // Jika string (1 option)
        if (is_string($options)) {
            return [trim($options)];
        }

        return [];
    }

    public function submitSurvey(Request $request, string $id)
    {
        $survey = Survey::findOrFail($id);

        // Cegah duplikat response
        $alreadyFilled = SurveyResponse::where('survey_id', $survey->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($alreadyFilled) {
            $response = response()->json([
                'status' => 'error',
                'message' => 'Anda sudah mengisi survey ini'
            ]);
        } else {
            DB::beginTransaction();
            try {

                // 1. Simpan response (header)
                $response = SurveyResponse::create([
                    'survey_id' => $survey->id,
                    'user_id'   => auth()->id(), // nullable jika guest
                ]);

                // 2. Simpan jawaban
                foreach ($request->answers as $questionId => $answer) {

                    // Jika checkbox (array)
                    if (is_array($answer)) {
                        SurveyAnswer::create([
                            'response_id' => $response->id,
                            'question_id' => $questionId,
                            'answer_json' => $answer
                        ]);
                    }
                    // Jika radio / select / text
                    else {
                        SurveyAnswer::create([
                            'response_id' => $response->id,
                            'question_id' => $questionId,
                            'answer_text' => $answer
                        ]);
                    }
                }

                DB::commit();

                $response = response()->json(['status' => 'success', 'message' => 'Berhasil Di Simpan', 'redirect' => route('document.index')]);
            } catch (\Throwable $throw) {
                DB::rollBack();
                Log::error($throw);
                $response = response()->json(['status' => 'error', 'message' => $throw->getMessage()]);
            }
        }
        return $response;
    }

    public function isiSurvey($layanan, $kode_satker)
    {
        $config['title'] = "Isi Survey";
        $config['kode_satker'] = $kode_satker;
        // ambil survey + questions + active options
        $survey = Survey::with(['questions' => function ($q) {
            $q->orderBy('order');
        }, 'questions.options' => function ($opt) {
            $opt->where('is_active', 1); // hanya option aktif
        }])->where('layanan_id', $layanan)->first();

        if ($survey == null) {
            abort(403, 'Survey Untuk Layanan Ini belum tersedia');
        }

        return view('backend.survey.show', compact('survey', 'config'));
    }
}
