<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan';

    protected $fillable = [
        'bidang_id',
        'nama_layanan',
        'slug',
        'deskripsi',
        'status',
    ];

    // Relasi ke bidang
    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    public function persyaratans()
    {
        return $this->belongsToMany(Persyaratan::class, 'layanan_persyaratan')
            ->withPivot('id', 'urut', 'wajib')
            ->withTimestamps()
            ->orderBy('layanan_persyaratan.urut', 'asc');
    }
}
