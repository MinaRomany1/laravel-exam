<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;
    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    protected $fillable = [
        'name',
        'user_id',
        'is_active',
        'duration_minutes',
        'description',
        'category_id',
        'start_time',
        'pass_percentage',
        'is_active',
        'pass_percentage',
        'end_time',
        'multiple_attempts',
        'shuffle_questions',
        'show_results',
        'show_detailed_result',
        'enable_image_answers'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class)->select('id', 'name', 'slug');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_questions')
            ->withPivot('order_index')
            ->orderBy('exam_questions.order_index');
    }

    public function examQuestions()
    {
        return $this->hasMany(ExamQuestion::class);
    }

    public function sessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }
    public function isAvailable(): bool
    {
        if (!$this->is_active) return false;

        if ($this->start_time && $this->start_time->isFuture()) return false;
        if ($this->end_time && $this->end_time->isPast()) return false;

        return true;
    }
}

