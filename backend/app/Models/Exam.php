<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    /** @use HasFactory<\Database\Factories\ExamFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'duration',
        'description',
        'category_id',
        'start_time',
        'end_time',
        'multiple_attempts',
        'shuffle_questions',
        'shuffle_answers',
        'show_results',
        'show_detailed_result',
        'enable_image_answers'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}

