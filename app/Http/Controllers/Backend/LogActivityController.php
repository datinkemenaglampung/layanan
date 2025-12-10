<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LogActivities;
use Yajra\DataTables\DataTables;

class LogActivityController extends Controller
{
    public function index(Request $request)
    {
        $config['title'] = "Log Activity";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Log Activity"],
        ];
        if ($request->ajax()) {
            $data = LogActivities::select('users.name', 'log_activities.*')
                ->leftjoin('users', 'users.id', '=', 'log_activities.user_id')
                ->orderby('created_at', 'DESC')
                ->limit(100)
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->make();
        }
        return view('backend.logactivity.index', compact('config'));
    }
}
