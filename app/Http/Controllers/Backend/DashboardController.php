<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Layanan;

class DashboardController extends Controller
{
    public function index()
    {
        $config['title'] = 'Dashboar';
        $config['layanan'] = Layanan::with('bidang')->where('status', 1)->get();
        return view('backend.dashboard.index', compact('config'));
    }

    public function graph(Request $request)
    {
        $year = $request['year'];
        $user = $request['user'];

        // dd($year, $user);
        // Hitung jumlah berita per bulan untuk tahun tertentu
        $statistics = Berita::select(
            DB::raw('MONTH(posted_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('posted_at', $year);
        if ($user !== null) {
            # code...
            $statistics->where('user_id', $user);
        }
        $statistics->groupBy('month');
        $statistics->orderBy('month', 'ASC');
        $statistics->get();

        // Format data untuk Chart.js
        $data = [
            'labels' => $statistics->pluck('month')->map(function ($month) {
                // Ubah angka bulan menjadi nama bulan
                return date('F', mktime(0, 0, 0, $month, 1));
            }),
            'data' => $statistics->pluck('total') // Ambil jumlah berita
        ];

        return response()->json($data);
    }

    public function pass()
    {
        // Hindari timeout
        set_time_limit(0);

        User::select('id', 'email')
            ->chunk(300, function ($users) {
                foreach ($users as $user) {
                    $user->update([
                        'password' => bcrypt($user->email),
                    ]);
                }
            });

        return response()->json([
            'status' => 'success',
            'message' => 'Reset password selesai'
        ]);
    }
}
