<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layanan;
use App\Models\Persyaratan;
use App\Models\LayananPersyaratan;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LayananController extends Controller
{
    function __construct()
    {
        $this->middleware('role:layanan-list', ['only' => ['index', 'show']]);
        $this->middleware('role:layanan-create', ['only' => ['create', 'store']]);
        $this->middleware('role:layanan-edit', ['only' => ['edit', 'update']]);
        $this->middleware('role:layanan-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $config['title'] = "Layanan";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Layanan"],
        ];
        if ($request->ajax()) {
            $data = Layanan::with('bidang')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a class="btn btn-success" href="' . route('layanan.edit', $row->id) . '"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-danger btn-delete" href="#" data-id ="' . $row->id . '" ><i class="fas fa-trash"></i></a>
                        <a class="btn btn-info" href="' . route('layanan.show', $row->id) . '"><i class="fas fa-edit"></i> Persyaratan</a>';
                    return $actionBtn;
                })->make();
        }
        return view('backend.layanan.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $config['title'] = "Tambah Layanan";
        $config['breadcrumbs'] = [
            ['url' => route('layanan.index'), 'title' => "Layanan"],
            ['url' => '#', 'title' => "Tambah Layanan"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('layanan.store')
        ];
        return view('backend.layanan.form', compact('config'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bidang_id' => 'required',
            'nama_layanan' => 'required',
            'slug' => 'required',
            'status' => 'required',
        ]);


        if ($validator->passes()) {

            DB::beginTransaction();
            try {
                $data = Layanan::create([
                    'bidang_id' => $request['bidang_id'],
                    'nama_layanan' => $request['nama_layanan'],
                    'slug' => $request['slug'],
                    'deskripsi' => $request['deskripsi'],
                    'status' => $request['status'],
                ]);

                DB::commit();
                $response = response()->json(['status' => 'success', 'message' => 'Berhasil Di Simpan', 'redirect' => route('layanan.index')]);
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
        $data = Layanan::findOrFail($id);
        $config['title'] = $data->nama_layanan;
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Menu Manager"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('layananPersyaratan.store')
        ];
        $config['layanan_id'] = $id;
        $sortable = self::getPersyaratan($id);
        // dd($sortable);
        return view('backend.layanan.list-persyaratan', compact('config', 'sortable'));
    }

    public function storePersyaratan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'layanan_id' => 'required',
            'persyaratan_id' => 'required',
            'wajib' => 'required',
            'uploaded_level' => 'required',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $menuManager = LayananPersyaratan::create([
                    'layanan_id' => $request['layanan_id'],
                    'persyaratan_id' => $request['persyaratan_id'],
                    'wajib' => $request['wajib'],
                    'uploaded_level' => $request['uploaded_level'],
                    'urut' => LayananPersyaratan::where([
                        ['layanan_id', $request['layanan_id']]
                    ])->max('urut') + 1
                ]);

                DB::commit();
                $response = response()->json(['status' => 'success', 'message' => 'Berhasil Di Simpan', 'redirect' => route('layanan.show', $request['layanan_id'])]);
            } catch (\Throwable $throw) {
                Log::error($throw);
                DB::rollBack();
                $response = response()->json(['status' => 'error', 'message' => $throw->getMessage()]);
            }
        } else {
            $response = response()->json(['status' => 'error', 'message' => $validator->errors()->all()]);
        }
        return $response;
    }

    public function deletePersyaratan($id, $idp)
    {
        $response = response()->json([
            'status' => 'error',
            'message' => 'Data gagal dihapus'
        ]);
        DB::beginTransaction();

        try {
            $data = LayananPersyaratan::where('layanan_id', $id)->where('persyaratan_id', $idp)->first();
            $data->delete();
            DB::commit();
            $response = response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Throwable $throw) {
            Log::error($throw);
            $response = response()->json(['error' => $throw->getMessage()]);
        }
        return $response;
    }

    public static function getPersyaratan($id)
    {
        $layanan = Layanan::with(['persyaratans' => function ($q) {
            $q->orderBy('layanan_persyaratan.urut', 'asc');
        }])->find($id);

        if (!$layanan) {
            return '<p>Layanan tidak ditemukan.</p>';
        }

        // return $layanan;

        return self::tree($layanan);
    }

    private static function tree($roots)
    {
        $html = '<ol class="dd-list">';

        foreach ($roots->persyaratans as $item) {
            $html .= '
        <li class="dd-item dd3-item" data-id="' . $item->pivot->id . '">
            <div class="dd-handle dd3-handle"></div>
            <div class="dd3-content">' . $item->nama_persyaratan . '</div>
            <div class="dd3-actions">
                <div class="btn-group">
                    ' . ($item->pivot->uploaded_level == 1 ? '<div class="fw-bold">A - </div>' : '') . '
                    ' . ($item->pivot->wajib == 1 ? '<div class="text-danger fw-bold">*</div>' : '') . '
                    <button type="button" class="btn btn-sm btn-delete btn-default" 
                        data-id="' . $roots->id . '" 
                        data-idp="' . $item->id . '">
                        <i class="fa fa-fw fa-trash"></i>
                    </button>
                </div>
            </div>
        </li>';
        }

        $html .= '</ol>';

        return $html;
    }

    public function changeHierarchy(Request $request)
    {
        $data = json_decode($request['hierarchy'], TRUE);
        $menuItems = $this->render_menu_hierarchy($data);

        DB::beginTransaction();
        try {
            foreach ($menuItems as $item) :
                $anu = LayananPersyaratan::find($item['id'])->update([
                    'urut' => $item['urut']
                ]);

            endforeach;
            DB::commit();
            $response = response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus',
                'redirect' => "reload"
            ]);
        } catch (\Throwable $throw) {
            DB::rollback();
            $response = response()->json([
                'status' => 'error',
                'message' => 'Gagal update data'
            ]);
        }
        return $response;
    }


    public function render_menu_hierarchy($data = array(), $result = array())
    {
        foreach ($data as $key => $val) {
            $row['id'] = $val['id'];
            $row['urut'] = ($key + 1);
            array_push($result, $row);
        }
        return $result;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $config['title'] = "Edit Layanan";
        $config['breadcrumbs'] = [
            ['url' => route('layanan.index'), 'title' => "Layanan"],
            ['url' => '#', 'title' => "Edit Layanan"],
        ];
        $data = Layanan::with('bidang')->where('id', $id)->first();
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('layanan.update', $id)
        ];
        return view('backend.layanan.form', compact('config', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'bidang_id' => 'required',
            'nama_layanan' => 'required',
            'status' => 'required',
        ]);


        if ($validator->passes()) {

            DB::beginTransaction();
            try {
                $data = Layanan::findOrFail($id);

                $data->update([
                    'bidang_id' => $request['bidang_id'],
                    'nama_layanan' => $request['nama_layanan'],
                    'deskripsi' => $request['deskripsi'],
                    'status' => $request['status'],
                ]);

                DB::commit();
                $response = response()->json(['status' => 'success', 'message' => 'Berhasil Di Simpan', 'redirect' => route('layanan.index')]);
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

        $data = Layanan::where('id', $id)->first();

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
        $data = Layanan::select('*')
            ->where('nama_layanan', 'LIKE', '%' . $request->q . '%')
            ->orderBy('nama_layanan')
            ->skip($offset)
            ->take($resultCount)
            ->selectRaw('id, nama_layanan as text')
            ->get();

        $count = Layanan::select('*')
            ->where('nama_layanan', 'LIKE', '%' . $request->q . '%')
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
