<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satker extends Model
{
    protected $table = 'satker';
    public $timestamps = false;

    protected $fillable = [
        'kode_satker',
        'kode_atasan',
        'kode_grup',
        'nama_satker',
        'keterangan',
        'slug',
    ];

    public function user()
    {
        return $this->hasMany(User::class, 'kode_satker', 'kode_satker');
    }
}
