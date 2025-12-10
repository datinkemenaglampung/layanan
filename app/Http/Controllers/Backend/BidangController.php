<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bidang;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BidangController extends Controller
{
    function __construct()
    {
        $this->middleware('role:bidang-list', ['only' => ['index', 'show']]);
        $this->middleware('role:bidang-create', ['only' => ['create', 'store']]);
        $this->middleware('role:bidang-edit', ['only' => ['edit', 'update']]);
        $this->middleware('role:bidang-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $config['title'] = "Bidang";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Bidang"],
        ];
        if ($request->ajax()) {
            $data = Bidang::get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a class="btn btn-success" href="' . route('bidang.edit', $row->id) . '"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-danger btn-delete" href="#" data-id ="' . $row->id . '" ><i class="fas fa-trash"></i></a>';
                    return $actionBtn;
                })->make();
        }
        return view('backend.bidang.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $config['title'] = "Tambah Bidang";
        $config['breadcrumbs'] = [
            ['url' => route('bidang.index'), 'title' => "Bidang"],
            ['url' => '#', 'title' => "Tambah Bidang"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('bidang.store')
        ];
        return view('backend.bidang.form', compact('config'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_bidang' => 'required',
        ]);


        if ($validator->passes()) {

            DB::beginTransaction();
            try {
                $data = Bidang::create([
                    'nama_bidang' => $request['nama_bidang'],
                    'deskripsi' => $request['deskripsi']
                ]);

                DB::commit();
                $response = response()->json(['status' => 'success', 'message' => 'Berhasil Di Simpan', 'redirect' => route('bidang.index')]);
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
        $config['title'] = "Edit Bidang";
        $config['breadcrumbs'] = [
            ['url' => route('bidang.index'), 'title' => "Bidang"],
            ['url' => '#', 'title' => "Edit Bidang"],
        ];
        $data = Bidang::where('id', $id)->first();
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('bidang.update', $id)
        ];
        return view('backend.bidang.form', compact('config', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_bidang' => 'required',
        ]);


        if ($validator->passes()) {

            $data = Bidang::where('id', $id)->first();

            DB::beginTransaction();
            try {
                $data->update([
                    'nama_bidang' => $request['nama_bidang'],
                    'deskripsi' => $request['deskripsi']
                ]);

                DB::commit();
                $response = response()->json(['status' => 'success', 'message' => 'Berhasil Di Simpan', 'redirect' => route('bidang.index')]);
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

        $data = Bidang::where('id', $id)->first();

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
        $data = Bidang::select('*')
            ->where('nama_bidang', 'LIKE', '%' . $request->q . '%')
            ->orderBy('nama_bidang')
            ->skip($offset)
            ->take($resultCount)
            ->selectRaw('id, nama_bidang as text')
            ->get();

        $count = Bidang::select('*')
            ->where('nama_bidang', 'LIKE', '%' . $request->q . '%')
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
