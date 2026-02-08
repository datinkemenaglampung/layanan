<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    protected $fillable = [
        'survey_id',
        'type',
        'question',
        'is_required',
        'order'
    ];

    public function options()
    {
        return $this->hasMany(SurveyOption::class, 'question_id');
    }
}
