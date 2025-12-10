<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanPersyaratan extends Model
{
    protected $table = 'permohonan_persyaratan';

    protected $fillable = [
        'permohonan_id',
        'persyaratan_id',
        'value',
        'status',
    ];

    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class);
    }

    public function persyaratan()
    {
        return $this->belongsTo(Persyaratan::class);
    }
}
