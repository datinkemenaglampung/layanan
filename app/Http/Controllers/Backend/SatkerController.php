<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Satker;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SatkerController extends Controller
{
    function __construct()
    {
        $this->middleware('role:satuan-kerja-list', ['only' => ['index', 'show']]);
        $this->middleware('role:satuan-kerja-create', ['only' => ['create', 'store']]);
        $this->middleware('role:satuan-kerja-edit', ['only' => ['edit', 'update']]);
        $this->middleware('role:satuan-kerja-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $config['title'] = "Satuan Kerja";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Satuan Kerja"],
        ];
        if ($request->ajax()) {
            $data = Satker::orderBy('kode_satker', 'ASC')
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a class="btn btn-success" href="' . route('satuan-kerja.edit', $row->id) . '"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-danger btn-delete" href="#" data-id ="' . $row->id . '" ><i class="fas fa-trash"></i></a>';
                    return $actionBtn;
                })->make();
        }
        return view('backend.satuan-kerja.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $config['title'] = "Tambah Satuan Kerja";
        $config['breadcrumbs'] = [
            ['url' => route('satuan-kerja.index'), 'title' => "Satuan Kerja"],
            ['url' => '#', 'title' => "Tambah Satuan Kerja"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('satuan-kerja.store')
        ];
        return view('backend.satuan-kerja.form', compact('config'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_satker' => 'required',
            'kode_atasan' => 'required',
            'kode_grup' => 'required',
            'nama_satker' => 'required',
            'slug' => 'required',
        ]);


        if ($validator->passes()) {

            DB::beginTransaction();
            try {
                $data = Satker::create([
                    'kode_satker' => $request['kode_satker'],
                    'kode_atasan' => $request['kode_atasan'],
                    'kode_grup' => $request['kode_grup'],
                    'nama_satker' => ucwords($request['nama_satker']),
                    'keterangan' => ucwords($request['keterangan']),
                    'slug' => $request->slug,
                ]);

                DB::commit();
                $response = response()->json(['status' => 'success', 'message' => 'Berhasil Di Simpan', 'redirect' => route('satuan-kerja.index')]);
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
        $config['title'] = "Edit Satuan Kerja";
        $config['breadcrumbs'] = [
            ['url' => route('satuan-kerja.index'), 'title' => "Satuan Kerja"],
            ['url' => '#', 'title' => "Edit Satuan Kerja"],
        ];
        $data = Satker::where('id', $id)->first();
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('satuan-kerja.update', $id)
        ];
        return view('backend.satuan-kerja.form', compact('config', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'kode_satker' => 'required',
            'kode_atasan' => 'required',
            'kode_grup' => 'required',
            'nama_satker' => 'required',
            'slug' => 'required',
        ]);


        if ($validator->passes()) {

            DB::beginTransaction();
            try {

                $data = Satker::where('id', $id)->first();

                $data->update([
                    'kode_satker' => $request['kode_satker'],
                    'kode_atasan' => $request['kode_atasan'],
                    'kode_grup' => $request['kode_grup'],
                    'nama_satker' => ucwords($request['nama_satker']),
                    'keterangan' => ucwords($request['keterangan']),
                    'slug' => $request->slug,
                ]);

                DB::commit();
                $response = response()->json(['status' => 'success', 'message' => 'Berhasil Di Simpan', 'redirect' => route('satuan-kerja.index')]);
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
        // $data = Kategori::first($id);
        $data = Satker::where('id', $id)->first();

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
        $data = Satker::select('*')
            ->where('nama_satker', 'LIKE', '%' . $request->q . '%')
            ->orderBy('nama_satker')
            ->skip($offset)
            ->take($resultCount)
            ->selectRaw('kode_satker, nama_satker as text')
            ->get();

        $count = Satker::select('*')
            ->where('nama_satker', 'LIKE', '%' . $request->q . '%')
            ->get()
            ->count();

        $endCount = $offset + $resultCount;
        $morePages = $count > $endCount;

        $results = array(
            "results" => $data->map(function ($category) {
                return [
                    'id' => $category->kode_satker,  // Correct id reference
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
