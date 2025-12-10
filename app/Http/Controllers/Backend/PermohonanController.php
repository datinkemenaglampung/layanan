<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permohonan;
use App\Models\Persyaratan;
use App\Models\Layanan;
use App\Models\PermohonanPersyaratan;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PermohonanController extends Controller
{
    function __construct()
    {
        $this->middleware('role:permohonan-list', ['only' => ['index', 'show']]);
        $this->middleware('role:permohonan-create', ['only' => ['create', 'store']]);
        $this->middleware('role:permohonan-edit', ['only' => ['edit', 'update']]);
        $this->middleware('role:permohonan-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $config['title'] = "Permohonan";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Permohonan"],
        ];
        if ($request->ajax()) {
            if (auth()->user()->role_id == 2) {
                $data = Permohonan::with(['layanan', 'user'])->where('users_id', auth()->user()->id)->get();
            } else {
                $data = Permohonan::with(['layanan', 'user'])->get();
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if (auth()->user()->role_id == 2) {
                        //
                        if ($row->status == 'menunggu') {
                            $actionBtn = '<a class="btn btn-success" href="' . route('permohonan.edit', $row->id) . '"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-danger btn-delete" href="#" data-id ="' . $row->id . '" ><i class="fas fa-trash"></i></a>';
                        } else if ($row->status == 'selesai') {
                            $actionBtn = '<a class="btn btn-primary" href="' . route('permohonan.edit', $row->id) . '"><i class="fas fa-eye"></i></a>';
                        } else {
                            $actionBtn = '<a class="btn btn-success" href="' . route('permohonan.edit', $row->id) . '"><i class="fas fa-edit"></i></a>';
                        }
                        //
                    } else {
                        $actionBtn = '<a class="btn btn-success" href="' . route('permohonan.edit', $row->id) . '"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-danger btn-delete" href="#" data-id ="' . $row->id . '" ><i class="fas fa-trash"></i></a>
                        <a class="btn btn-info" href="' . route('permohonan.show', $row->id) . '"><i class="fas fa-edit"></i> Periksa</a>';
                    }
                    return $actionBtn;
                })->make();
        }
        return view('backend.permohonan.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'layanan_id' => 'required|exists:layanan,id',
            'keterangan' => 'nullable|string',
            'files.*'    => 'file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        $layanan = Layanan::with('persyaratans')->findOrFail($request->layanan_id);

        // Validasi syarat wajib
        foreach ($layanan->persyaratans as $persyaratan) {
            if ($persyaratan->pivot->wajib == 1) {
                if (!$request->hasFile("files.{$persyaratan->id}")) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "File {$persyaratan->nama_persyaratan} wajib diupload."
                    ], 422);
                }
            }
        }

        DB::beginTransaction();
        try {

            // 1. Simpan permohonan
            $permohonan = Permohonan::create([
                'users_id'    => auth()->user()->id,
                'layanan_id' => $layanan->id,
                'keterangan' => $request->keterangan,
                'status'     => 'menunggu',
            ]);

            // 2. Upload dan simpan persyaratan
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $persyaratan_id => $file) {

                    $filename = auth()->user()->username . '_' . $permohonan->id . '_' . $persyaratan_id . '_' . time() . '.' . $file->getClientOriginalExtension();

                    // Simpan ke storage/app/public/permohonan
                    // $path = $file->storeAs('permohonan', $filename, 'public');
                    $file->move(public_path('storage/permohonan'), $filename);

                    PermohonanPersyaratan::create([
                        'permohonan_id'  => $permohonan->id,
                        'persyaratan_id' => $persyaratan_id,
                        'value'           => $filename,
                        'status'         => 'menunggu', // admin akan verifikasi
                        'catatan'        => null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => "success",
                'message' => "Permohonan berhasil diajukan!",
                'redirect' => route('permohonan.show', $permohonan->id)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => "error",
                'message' => "Terjadi kesalahan: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $config['title'] = "Permohonan";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Permohonan"],
        ];
        $permohonan = Permohonan::with([
            'layanan.persyaratans',
            'persyaratans' => function ($q) {
                $q->withPivot('id', 'value', 'status');
            }
        ])->findOrFail($id);

        if (auth()->user()->role_id == 2) {
            return redirect()->route('permohonan.index');
        }

        $permohonan->update([
            'status' => 'diproses',
        ]);

        return view('backend.permohonan.show', compact('config', 'permohonan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $config['title'] = "Permohonan";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Permohonan"],
        ];
        $permohonan = Permohonan::with([
            'layanan.persyaratans',
            'persyaratans' => function ($q) {
                $q->withPivot('value', 'status', 'catatan');
            }
        ])->findOrFail($id);

        if (auth()->user()->role_id !== 1 && $permohonan->users_id !== auth()->user()->id) {
            return redirect()->route('permohonan.index');
        }

        return view('backend.permohonan.edit', compact('config', 'permohonan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $permohonan = Permohonan::with('layanan.persyaratans')->findOrFail($id);

        // if ($permohonan->user_id != auth()->id()) {
        //     abort(403, 'Tidak boleh mengedit permohonan ini');
        // }

        DB::beginTransaction();
        try {

            // Update keterangan
            $permohonan->update([
                'keterangan' => $request->keterangan
            ]);

            // Jika ada file diupload
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $persyaratan_id => $file) {

                    $pivot = PermohonanPersyaratan::where('permohonan_id', $permohonan->id)
                        ->where('persyaratan_id', $persyaratan_id)
                        ->first();

                    $filename = uth()->user()->username . '_' . $permohonan->id . '_' . $persyaratan_id . '_' . time() . '.' . $file->getClientOriginalExtension();
                    // $path = $file->storeAs('permohonan', $filename, 'public');
                    $file->move(public_path('storage/permohonan'), $filename);

                    if ($pivot) {
                        // Update file & reset status menjadi menunggu ulang
                        $pivot->update([
                            'value' => $filename,
                            'status' => 'menunggu',
                            'catatan' => null
                        ]);
                    } else {
                        // Jika sebelumnya belum pernah upload (jarang terjadi tapi aman)
                        PermohonanPersyaratan::create([
                            'permohonan_id' => $permohonan->id,
                            'persyaratan_id' => $persyaratan_id,
                            'value' => $filename,
                            'status' => 'menunggu'
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('permohonan.show', $permohonan->id)
                ->with('success', 'Permohonan berhasil diupdate, menunggu verifikasi admin.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function pengajuan($slug)
    {
        $config['title'] = "Tambah Permohonan";
        $config['breadcrumbs'] = [
            ['url' => route('permohonan.index'), 'title' => "Permohonan"],
            ['url' => '#', 'title' => "Tambah Permohonan"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('permohonan.store')
        ];

        $layanan = Layanan::with('persyaratans')->where('slug', $slug)->first();

        return view('backend.permohonan.pengajuan', compact('config', 'layanan'));
    }

    public function verify(Request $request, $pivot_id)
    {
        $request->validate([
            'status' => 'required|in:sesuai,tidak sesuai',
            'catatan' => 'nullable|string'
        ]);

        $pivot = PermohonanPersyaratan::findOrFail($pivot_id);
        $pivot->update([
            'status' => $request->status,
            'catatan' => $request->catatan
        ]);

        return back()->with('success', 'Status persyaratan berhasil diperbarui.');
    }
}
