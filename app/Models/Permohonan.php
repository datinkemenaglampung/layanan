<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permohonan extends Model
{
    protected $table = 'permohonan';

    protected $fillable = [
        'users_id',
        'layanan_id',
        'status',
        'status_level',
        'keterangan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    public function persyaratans()
    {
        return $this->belongsToMany(Persyaratan::class, 'permohonan_persyaratan')
            ->withPivot('value', 'status')
            ->withTimestamps();
    }
}
