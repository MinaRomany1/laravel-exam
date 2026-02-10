<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ExamAttemptAnswer;
class ExamAttempt extends Model
{
    protected $fillable = [
        'exam_session_id',
        'user_id',
        'score',
        'started_at',
        'finished_at',
        'submitted',
    ];

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function answers()
    {
        return $this->hasMany(ExamAttemptAnswer::class);
    }
}
