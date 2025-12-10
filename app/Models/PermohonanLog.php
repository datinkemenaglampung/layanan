<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanLog extends Model
{
    protected $table = 'permohonan_log';

    protected $fillable = [
        'permohonan_id',
        'users_id',
        'catatan',
    ];
}
