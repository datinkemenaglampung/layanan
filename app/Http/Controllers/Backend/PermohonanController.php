<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permohonan;
use App\Models\Persyaratan;
use App\Models\Layanan;
use App\Models\PermohonanLog;
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

    private function getKet() {}
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
            $user = auth()->user();

            if ($user->role_id == 2) {

                // USER BIASA: hanya permohonan milik sendiri
                $data = Permohonan::with(['layanan', 'user'])
                    ->where('users_id', $user->id)
                    ->get();
            } elseif ($user->role_id == 3) {

                // ADMIN Daerah: hanya layanan yang ditugaskan
                $data = Permohonan::with(['layanan', 'user'])
                    ->where('status_level', Auth()->user()->KODE_SATKER_3)
                    ->get();
            } elseif ($user->role_id == 5) {

                // ADMIN KANWIL: hanya layanan yang ditugaskan
                $layananIds = $user->layanan_ids ?? [];

                $data = Permohonan::with(['layanan', 'user'])
                    ->whereIn('layanan_id', $layananIds)
                    ->where('status_level', '02090100000000')
                    ->get();
            } else {

                // SUPER / ADMIN PUSAT: lihat semua
                $data = Permohonan::with(['layanan', 'user'])->get();
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $tlBtn = '<a class="btn btn-secondary btnTimeline" href="javascript:void(0)" data-id="' . $row->id . '"><i class="fas fa-clock"></i></a>';
                    if (auth()->user()->role_id == 2) {
                        //
                        if ($row->status == 'dibuat') {
                            $actionBtn = '<a class="btn btn-success" href="' . route('permohonan.edit', $row->id) . '"><i class="fas fa-edit"></i></a>
                                            <a class="btn btn-danger btn-delete" href="#" data-id ="' . $row->id . '"><i class="fas fa-trash"></i></a>';
                        } else if ($row->status == 'selesai') {
                            $actionBtn = '<a class="btn btn-primary" href="' . route('permohonan.edit', $row->id) . '"><i class="fas fa-eye"></i></a>';
                        } else {
                            $actionBtn = '<a class="btn btn-success" href="' . route('permohonan.edit', $row->id) . '"><i class="fas fa-edit"></i></a>';
                        }
                    } else {

                        //
                        if ($row->status_level = '02090100000000') {
                            # code...
                            $actionBtn = '<a class="btn btn-success" href="' . route('permohonan.edit', $row->id) . '"><i class="fas fa-edit"></i></a>
                                        <a class="btn btn-danger btn-delete" href="#" data-id ="' . $row->id . '"><i class="fas fa-trash"></i></a>
                                        <a class="btn btn-info" href="' . route('permohonan.show', $row->id) . '"><i class="fas fa-edit"></i> Periksa</a>';
                        } else {

                            $actionBtn = '<a class="btn btn-success" href="' . route('permohonan.edit', $row->id) . '"><i class="fas fa-edit"></i></a>
                                        <a class="btn btn-danger btn-delete" href="#" data-id ="' . $row->id . '"><i class="fas fa-trash"></i></a>
                                        <a class="btn btn-info" href="' . route('permohonan.show', $row->id) . '"><i class="fas fa-edit"></i> Periksa</a>
                                        <a class="btn btn-primary btn-post" href="#" data-id ="' . $row->id . '"><i class="fas fa-paper-plane"></i> Ajukan</a>';
                        }
                    }

                    return $actionBtn . ' ' . $tlBtn;
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

        $layanan = Layanan::with(['persyaratans' => function ($q) {
            if (auth()->user()->role_id == 2) {
                $q->where('uploaded_level', '!=', '1');
            }
        }])->findOrFail($request->layanan_id);

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
                'status'     => 'diajukan',
                'status_level' => auth()->user()->KODE_SATKER_3,
            ]);

            $permohonanLog = PermohonanLog::create([
                'permohonan_id' => $permohonan->id,
                'users_id'    => auth()->user()->id,
                'catatan' => $permohonan->status . ' ' . auth()->user()->name,
            ]);

            // 2. Upload dan simpan persyaratan
            if ($request->hasFile('files')) {

                $username = $permohonan->user->username;
                $basePath = public_path('storage/permohonan/' . $username);

                // Buat folder jika belum ada
                if (!file_exists($basePath)) {
                    mkdir($basePath, 0777, true);
                }

                foreach ($request->file('files') as $persyaratan_id => $file) {

                    $filename = $username . '_' . $permohonan->id . '_' . $persyaratan_id . '_' . time() . '.' . $file->getClientOriginalExtension();

                    // Simpan file ke folder username
                    $file->move($basePath, $filename);

                    PermohonanPersyaratan::create([
                        'permohonan_id'  => $permohonan->id,
                        'persyaratan_id' => $persyaratan_id,
                        'value'          => $filename,
                        'status'          => 'upload',
                    ]);
                }
            }

            DB::commit();

            $response = response()->json(['status' => 'success', 'message' => 'Berhasil Di Simpan', 'redirect' => route('permohonan.index')]);
        } catch (\Exception $e) {
            DB::rollback();
            $response = response()->json([
                'status' => "error",
                'message' => "Terjadi kesalahan: " . $e->getMessage()
            ], 500);
        }

        return $response;
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
                $q->withPivot('id', 'value', 'status', 'catatan');
            }
        ])->findOrFail($id);

        if (auth()->user()->role_id == 2) {
            return redirect()->route('permohonan.index');
        }

        //

        if ($permohonan->status !== 'diproses') {
            $permohonan->update([
                'status' => 'diproses',
            ]);

            $permohonanLog = PermohonanLog::create([
                'permohonan_id' => $permohonan->id,
                'users_id'    => auth()->user()->id,
                'catatan' => $permohonan->status . ' ' . auth()->user()->name,
            ]);
        }

        return view('backend.permohonan.show', compact('config', 'permohonan'));
    }

    /* verifikasi berkas */

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

    /* Review Proses */
    public function reviewComplete(Request $request, $id)
    {
        $permohonan = Permohonan::with('persyaratans')->findOrFail($id);

        // ================= REVIEW DOKUMEN =================
        $jumlahTidakSesuai = $permohonan->persyaratans
            ->where('pivot.status', 'tidak sesuai')
            ->count();

        PermohonanLog::create([
            'permohonan_id' => $permohonan->id,
            'users_id'      => auth()->id(),
            'catatan'       => $jumlahTidakSesuai > 0
                ? "Ada {$jumlahTidakSesuai} dokumen TIDAK SESUAI pada proses review."
                : "Dokumen sudah sesuai semua."
        ]);

        // ================= STATUS & KETERANGAN (KANWIL SAJA) =================
        if (
            $jumlahTidakSesuai > 0 &&
            $request->filled('status_permohonan')
        ) {
            $permohonan->update([
                'status'     => $request->status_permohonan,
                'keterangan' => $request->keterangan
            ]);

            PermohonanLog::create([
                'permohonan_id' => $permohonan->id,
                'users_id'      => auth()->id(),
                'catatan'       => "Status permohonan: {$request->status_permohonan}. {$request->keterangan}"
            ]);
        }

        return response()->json([
            'status'   => 'success',
            'message'  => 'Berhasil Disimpan',
            'redirect' => route('permohonan.index')
        ]);
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
                'keterangan' => $request->keterangan,
            ]);

            // Jika ada file diupload
            if ($request->hasFile('files')) {

                $username = $permohonan->user->username;
                $basePath = public_path('storage/permohonan/' . $username);

                // Buat folder jika belum ada
                if (!file_exists($basePath)) {
                    mkdir($basePath, 0777, true);
                }

                foreach ($request->file('files') as $persyaratan_id => $file) {

                    // Ambil data pivot
                    $pivot = PermohonanPersyaratan::where('permohonan_id', $permohonan->id)
                        ->where('persyaratan_id', $persyaratan_id)
                        ->first();

                    // Buat filename baru
                    $filename = $username . '_' . $permohonan->id . '_' . $persyaratan_id . '_' . time() . '.' . $file->getClientOriginalExtension();

                    // Hapus file lama kalau ada
                    if ($pivot && $pivot->value) {
                        $oldFile = $basePath . '/' . $pivot->value; // hanya nama file
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                    }

                    // Upload file baru
                    $file->move($basePath, $filename);

                    // Simpan ke DB (hanya nama file)
                    if ($pivot) {

                        $pivot->update([
                            'value'   => $filename,
                            'status'  => 'perbaikan',
                            'catatan' => null
                        ]);

                        PermohonanLog::create([
                            'permohonan_id' => $permohonan->id,
                            'users_id'      => auth()->id(),
                            'catatan'       => "Dokumen Perbaikan Di Ajukan."
                        ]);
                    } else {

                        PermohonanPersyaratan::create([
                            'permohonan_id'  => $permohonan->id,
                            'persyaratan_id' => $persyaratan_id,
                            'value'          => $filename,
                            'status'         => 'upload'
                        ]);

                        PermohonanLog::create([
                            'permohonan_id' => $permohonan->id,
                            'users_id'      => auth()->id(),
                            'catatan'       => "Dokumen Baru Di Upload."
                        ]);
                    }
                }
            }


            DB::commit();

            $response = response()->json(['status' => 'success', 'message' => 'Berhasil Di Simpan', 'redirect' => route('permohonan.index')]);
        } catch (\Exception $e) {
            DB::rollback();
            $response = response()->json([
                'status' => "error",
                'message' => "Terjadi kesalahan: " . $e->getMessage()
            ], 500);
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

        $layanan = Layanan::where('slug', $slug)
            ->with(['persyaratans' => function ($q) {
                if (auth()->user()->role_id == 2) {
                    $q->where('uploaded_level', '!=', '1');
                }
            }])
            ->first();

        return view('backend.permohonan.pengajuan', compact('config', 'layanan'));
    }

    public function ajukan($id)
    {
        $permohonan = Permohonan::with('persyaratans')->findOrFail($id);
        $persyaratans = $permohonan->persyaratans;

        // Cek file belum upload
        $jumlahBelumUpload = $persyaratans->filter(function ($p) {
            return empty($p->pivot->value);
        })->count();

        if ($jumlahBelumUpload > 0) {
            return response()->json([
                'status' => 'error',
                'message' => "Ada $jumlahBelumUpload dokumen belum diupload",
            ]);
        }

        // Jika lengkap semua -> ajukan ke Kanwil
        $permohonan->update([
            'status' => 'diajukan',
            'status_level' => '02090100000000',
        ]);

        PermohonanLog::create([
            'permohonan_id' => $permohonan->id,
            'users_id'      => auth()->user()->id,
            'catatan'       => 'Permohonan diteruskan ke Kanwil.'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Permohonan berhasil diajukan ke Kanwil",
        ]);
    }
}
