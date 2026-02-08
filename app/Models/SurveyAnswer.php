<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyAnswer extends Model
{
    protected $fillable = [
        'response_id',
        'question_id',
        'answer_text',
        'answer_json'
    ];

    protected $casts = [
        'answer_json' => 'array',
    ];

    public function question()
    {
        return $this->belongsTo(SurveyQuestion::class);
    }
}
