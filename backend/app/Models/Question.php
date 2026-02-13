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
        'question_type',
        'image_url'
    ];

    public function exams()
    {
        return $this->belongsToMany(Exam::class)
            ->withPivot('order_index');
    }
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
    public function correctAnswer()
    {
        return $this->hasOne(Answer::class)->where('is_correct', true);
    }
    public function examQuestions()
    {
        return $this->hasMany(ExamQuestion::class)
            ->orderBy('order_index');
    }
}
