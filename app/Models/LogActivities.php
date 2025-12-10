<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogActivities extends Model
{
    protected $table = 'log_activities';

    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'description',
        'ip_address'
    ];
}
