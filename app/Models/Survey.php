<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = [
        'title',
        'description',
        'is_active',
        'layanan_id',
        'slug',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('order');
    }
}
