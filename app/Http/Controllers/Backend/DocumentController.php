<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    function __construct()
    {
        $this->middleware('role:document-list', ['only' => ['index', 'show']]);
        $this->middleware('role:document-create', ['only' => ['create', 'store']]);
        $this->middleware('role:document-edit', ['only' => ['edit', 'update']]);
        $this->middleware('role:document-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $config['title'] = "Document";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Document"],
        ];
        if ($request->ajax()) {
            $data = Document::get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a class="btn btn-success" href="' . route('document.edit', $row->id) . '"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-danger btn-delete" href="#" data-id ="' . $row->id . '" ><i class="fas fa-trash"></i></a>';
                    return $actionBtn;
                })->make();
        }
        return view('backend.document.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $config['title'] = "Tambah Document";
        $config['breadcrumbs'] = [
            ['url' => route('document.index'), 'title' => "Document"],
            ['url' => '#', 'title' => "Tambah Document"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('document.store')
        ];
        return view('backend.document.form', compact('config'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'file' => 'required|mimes:jpg,png,jpeg,gif,pdf,docx,xlsx|max:4048',
        ]);


        if ($validator->passes()) {

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $extension = $file->getClientOriginalExtension();
                $filename =  date('Y-m-d_His') . '_document.' . $extension;
                // $file->storeAs('public/cover/', $filename);
                $file->move(public_path('storage/document'), $filename);
            } else {
                $filename = '';
            }

            DB::beginTransaction();
            try {
                $data = Document::create([
                    'nama' => $request['nama'],
                    'file' => $filename
                ]);

                DB::commit();
                $response = response()->json(['status' => 'success', 'message' => 'Berhasil Di Simpan', 'redirect' => route('document.index')]);
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

        $config['title'] = "Edit Document";
        $config['breadcrumbs'] = [
            ['url' => route('document.index'), 'title' => "Document"],
            ['url' => '#', 'title' => "Edit Document"],
        ];
        $data = Document::where('id', $id)->first();
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('document.update', $id)
        ];
        return view('backend.document.form', compact('config', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'file' => 'required|mimes:jpg,png,jpeg,gif,pdf,docx,xlsx|max:4048',
        ]);

        if ($validator->passes()) {

            $data = Document::where('id', $id)->first();

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $extension = $file->getClientOriginalExtension();
                $filename =  date('Y-m-d_His') . '_document.' . $extension;
                // $file->storeAs('public/cover/', $filename);
                $file->move(public_path('storage/document'), $filename);
            } else {
                $filename = $data->file;
            }

            DB::beginTransaction();
            try {
                $data->update([
                    'nama' => $request['kode_satker'],
                    'file' => $filename
                ]);

                DB::commit();
                $response = response()->json(['status' => 'success', 'message' => 'Berhasil Di Simpan', 'redirect' => route('document.index')]);
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

        $data = Document::where('id', $id)->first();

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
}
