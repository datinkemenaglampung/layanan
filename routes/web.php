<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth as Auth;
use App\Http\Controllers\Backend as Backend;
use App\Http\Controllers\Frontend as Frontend;

route::get('/', function () {
    return redirect()->route('login');
});

Route::prefix('auth')->group(function () {
    Route::get('/', [Auth\LoginController::class, 'showLoginForm']);
    Route::post('/', [Auth\LoginController::class, 'login'])->name('login');
    Route::get('/logout', [Auth\LoginController::class, 'logout'])->name('logout');

    /* SSO */
    Route::get('/sso', [Auth\SSOController::class, 'redirectToProvider'])->name('loginSSO');
    Route::get('/callback', [Auth\SSOController::class, 'handleProviderCallback']);
});

Route::prefix('backend')->middleware(['auth'])->group(function () {
    /* Role Route */
    Route::get('roles/select2', [Backend\RoleController::class, 'select2'])->name('roles.select2');
    Route::resource('roles', Backend\RoleController::class);

    /* Menu Manager Route */
    Route::resource('menu-manager', Backend\MenuManagerController::class);
    Route::post('menu-manager/changeHierarchy', [Backend\MenuManagerController::class, 'changeHierarchy'])->name('menu-manager.changeHierarchy');


    /* Dashboard */
    Route::get('dashboard', [Backend\DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/pass', [Backend\DashboardController::class, 'pass']);
    Route::post('dashboard/graph', [Backend\DashboardController::class, 'graph'])->name('dashboard.graph');

    /* User Route */
    Route::get('users/sync-user', [Backend\UserController::class, 'sync'])->name('users.sync');
    Route::get('users/select2', [Backend\UserController::class, 'select2'])->name('users.select2');
    Route::get('user/profile', [Backend\UserController::class, 'profile'])->name('users.profile');
    Route::post('user/profile/post', [Backend\UserController::class, 'profile_store'])->name('users.profileStore');
    Route::resource('users', Backend\UserController::class);
    Route::post('reset-password-users', [Backend\UserController::class, 'resetpassword'])->name('users.reset-password-users');
    Route::get('change-password', [Backend\UserController::class, 'changepassword'])->name('change-password');
    Route::post('update-change-password', [Backend\UserController::class, 'updatechangepassword'])->name('update-change-password');

    /* Kolom */
    Route::get('satuan-kerja/select2', [Backend\SatkerController::class, 'select2'])->name('satuan-kerja.select2');
    Route::resource('satuan-kerja', Backend\SatkerController::class);

    /* Document*/
    Route::resource('document', Backend\DocumentController::class);

    /* log */
    Route::get('logactivity', [Backend\LogActivityController::class, 'index'])->name('logactivity.index');

    /* Bidang */
    Route::get('bidang/select2', [Backend\BidangController::class, 'select2'])->name('bidang.select2');
    Route::resource('bidang', Backend\BidangController::class);

    /* Layanan */
    Route::get('layanan/select2', [Backend\LayananController::class, 'select2'])->name('layanan.select2');
    Route::delete('layanan/deletePersyaratan/{layanan_id}/{persyaratan_id}', [Backend\LayananController::class, 'deletePersyaratan'])->name('layananPersyaratan.delete');
    Route::post('layanan/storePersyaratan', [Backend\LayananController::class, 'storePersyaratan'])->name('layananPersyaratan.store');
    Route::post('layanan/changeHierarchy', [Backend\LayananController::class, 'changeHierarchy'])->name('list-persyaratan.changeHierarchy');
    Route::resource('layanan', Backend\LayananController::class);

    /* Permohonan */
    Route::get('permohonan/layanan/{slug}', [Backend\PermohonanController::class, 'pengajuan'])->name('permohonan.pengajuan');
    Route::post('permohonan/verify/{pivot_id}', [Backend\PermohonanController::class, 'verify'])->name('permohonan.verify');
    Route::post('permohonan/{id}/review-complete', [Backend\PermohonanController::class, 'reviewComplete'])->name('permohonan.reviewComplete');
    Route::post('permohonan/ajukan/{id}', [Backend\PermohonanController::class, 'ajukan'])->name('permohonan.ajukan');
    Route::resource('permohonan', Backend\PermohonanController::class);

    Route::get('/permohonan/{id}/timeline', function ($id) {
        $logs = \App\Models\PermohonanLog::with(['user', 'permohonan'])
            ->where('permohonan_id', $id)
            ->orderBy('created_at')
            ->get();

        return response()->json($logs);
    });

    /* Persyaratan */
    Route::get('persyaratan/select2', [Backend\PersyaratanController::class, 'select2'])->name('persyaratan.select2');
    Route::resource('persyaratan', Backend\PersyaratanController::class);
});
