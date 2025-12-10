<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persyaratan extends Model
{
    protected $table = 'persyaratan';

    protected $fillable = [
        'nama_persyaratan',
        'deskripsi',
        'tipe_input',   // text, file, number, select, dll
    ];

    public function layanan()
    {
        return $this->belongsToMany(Layanan::class, 'layanan_persyaratan')
            ->withPivot('urut')
            ->withTimestamps();
    }
}
