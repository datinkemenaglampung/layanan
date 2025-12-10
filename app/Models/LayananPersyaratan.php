<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LayananPersyaratan extends Model
{
    protected $table = 'layanan_persyaratan';

    protected $fillable = [
        'layanan_id',
        'persyaratan_id',
        'wajib',
        'urut',
        'uploaded_level',
    ];
}
