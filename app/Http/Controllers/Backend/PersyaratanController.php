<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Persyaratan;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PersyaratanController extends Controller
{
    function __construct()
    {
        $this->middleware('role:persyaratan-list', ['only' => ['index', 'show']]);
        $this->middleware('role:persyaratan-create', ['only' => ['create', 'store']]);
        $this->middleware('role:persyaratan-edit', ['only' => ['edit', 'update']]);
        $this->middleware('role:persyaratan-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $config['title'] = "Persyaratan";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Persyaratan"],
        ];
        if ($request->ajax()) {
            $data = Persyaratan::get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a class="btn btn-success" href="' . route('persyaratan.edit', $row->id) . '"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-danger btn-delete" href="#" data-id ="' . $row->id . '" ><i class="fas fa-trash"></i></a>';
                    return $actionBtn;
                })->make();
        }
        return view('backend.persyaratan.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $config['title'] = "Tambah Persyaratan";
        $config['breadcrumbs'] = [
            ['url' => route('persyaratan.index'), 'title' => "Persyaratan"],
            ['url' => '#', 'title' => "Tambah Persyaratan"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('persyaratan.store')
        ];
        return view('backend.persyaratan.form', compact('config'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_persyaratan' => 'required',
            'tipe_input' => 'required',
        ]);


        if ($validator->passes()) {

            DB::beginTransaction();
            try {
                $data = Persyaratan::create([
                    'nama_persyaratan' => $request['nama_persyaratan'],
                    'deskripsi' => $request['deskripsi'],
                    'tipe_input' => $request['tipe_input'],
                ]);

                DB::commit();
                $response = response()->json(['status' => 'success', 'message' => 'Berhasil Di Simpan', 'redirect' => route('persyaratan.index')]);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $config['title'] = "Edit Persyaratan";
        $config['breadcrumbs'] = [
            ['url' => route('persyaratan.index'), 'title' => "Persyaratan"],
            ['url' => '#', 'title' => "Edit Persyaratan"],
        ];
        $data = Persyaratan::where('id', $id)->first();
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('persyaratan.update', $id)
        ];
        return view('backend.persyaratan.form', compact('config', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $validator = Validator::make($request->all(), [
            'nama_persyaratan' => 'required',
            'tipe_input' => 'required',
        ]);


        if ($validator->passes()) {

            DB::beginTransaction();
            try {
                $data = Persyaratan::findOrFail($id);

                $data->update([
                    'nama_persyaratan' => $request['nama_persyaratan'],
                    'deskripsi' => $request['deskripsi'],
                    'tipe_input' => $request['tipe_input'],
                ]);

                DB::commit();
                $response = response()->json(['status' => 'success', 'message' => 'Berhasil Di Simpan', 'redirect' => route('persyaratan.index')]);
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
        $response = response()->json([
            'status' => 'error',
            'message' => 'Data gagal dihapus'
        ]);

        $data = Persyaratan::where('id', $id)->first();

        DB::beginTransaction();
        try {
            $data->delete();

            DB::commit();
            $response = response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Throwable $throw) {
            Log::error($throw);
            $response = response()->json([
                'status' => 'error',
                'message' => $throw->getMessage()
            ]);
        }
        return $response;
    }

    public function select2(Request $request)
    {
        $page = $request->page;
        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;
        $data = Persyaratan::select('*')
            ->where('nama_persyaratan', 'LIKE', '%' . $request->q . '%')
            ->orderBy('nama_persyaratan')
            ->skip($offset)
            ->take($resultCount)
            ->selectRaw('id, nama_persyaratan as text')
            ->get();

        $count = Persyaratan::select('*')
            ->where('nama_persyaratan', 'LIKE', '%' . $request->q . '%')
            ->get()
            ->count();

        $endCount = $offset + $resultCount;
        $morePages = $count > $endCount;

        $results = array(
            "results" => $data->map(function ($category) {
                return [
                    'id' => $category->id,  // Correct id reference
                    'text' => $category->text,  // Correct text reference
                    'ariaSelected' => false // Optional, can adjust based on selection logic
                ];
            }),
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
}
