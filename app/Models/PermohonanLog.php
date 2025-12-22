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

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class, 'permohonan_id');
    }
}
