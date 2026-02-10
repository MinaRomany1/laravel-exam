<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    /** @use HasFactory<\Database\Factories\QuestionFactory> */
    use HasFactory;

    protected $fillable = [
        'question_text',
        'exam_id',
        'question_type',
        'image_url'
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
