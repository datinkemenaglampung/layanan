<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Models\LogActivities;

class LogActivity
{
    public static function add($action, $model, $model_id, $description = null)
    {
        LogActivities::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model' => $model,
            'model_id' => $model_id,
            'description' => $description,
            'ip_address' => Request::ip(),
        ]);
    }
}
