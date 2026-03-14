<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ExamAttemptAnswer;
use App\Models\Exam;
use App\Models\ExamSession;

class ExamAttempt extends Model
{
    protected $fillable = [
        'exam_id',
        'exam_session_id',
        'score',
        'passed',
        'started_at',
        'finished_at',
        'submitted',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAttemptAnswer::class);
    }
}
