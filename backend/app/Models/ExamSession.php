<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ExamAttempt;
class ExamSession extends Model
{
    /** @use HasFactory<\Database\Factories\ExamSessionFactory> */
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'user_id',
        'token',
        'started_at',
        'expires_at',
        'submitted_at',
        'active'
    ];
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }
}
