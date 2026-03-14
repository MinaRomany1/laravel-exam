<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use App\Models\ExamSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExamSessionService
{
    /**
     * Start or resume an exam session for the given user.
     *
     * Returns an array payload containing:
     *  - session: ExamSession
     *  - exam: Exam (with category relation loaded)
     *  - attempt: ExamAttempt|null (with answers.question.answers loaded)
     *  - status_code: int (HTTP status code to use in controller)
     */
    public function startExam(Exam $exam, User $user): array
    {
        $now = Carbon::now();

        $exam->load('category');

        $existingSession = $this->findActiveSession($exam, $user, $now);

        if ($existingSession) {
            $attempt = $existingSession->attempts()->latest('id')->first();

            if ($attempt) {
                $this->loadAttemptRelations($attempt);
            }

            return [
                'session' => $existingSession,
                'exam' => $exam,
                'attempt' => $attempt,
                'status_code' => 200,
            ];
        }

        return $this->createSessionAndAttempt($exam, $user, $now);
    }

    private function findActiveSession(Exam $exam, User $user, Carbon $now): ?ExamSession
    {
        return ExamSession::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->where('active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', $now);
            })
            ->first();
    }

    private function createSessionAndAttempt(Exam $exam, User $user, Carbon $now): array
    {
        return DB::transaction(function () use ($exam, $user, $now) {
            $expiresAt = null;

            if (!is_null($exam->duration_minutes)) {
                $expiresAt = $now->copy()->addMinutes($exam->duration_minutes);
            }

            $session = ExamSession::create([
                'exam_id' => $exam->id,
                'user_id' => $user->id,
                'token' => Str::uuid()->toString(),
                'started_at' => $now,
                'expires_at' => $expiresAt,
                'active' => true,
            ]);

            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'exam_session_id' => $session->id,
                'score' => null,
                'passed' => false,
                'started_at' => $now,
                'finished_at' => null,
                'submitted' => false,
            ]);

            $questions = $exam->questions()
                ->with('answers:id,question_id,answer_text')
                ->get();

            if ($exam->shuffle_questions) {
                $questions = $questions->shuffle()->values();
            }

            $answerRows = [];

            foreach ($questions as $index => $question) {
                $answerRows[] = [
                    'exam_attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'answer_id' => null,
                    'text_answer' => null,
                    'is_correct' => null,
                    'awarded_mark' => null,
                    'order_index' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (!empty($answerRows)) {
                ExamAttemptAnswer::insert($answerRows);
            }

            $this->loadAttemptRelations($attempt);

            return [
                'session' => $session,
                'exam' => $exam,
                'attempt' => $attempt,
                'status_code' => 201,
            ];
        });
    }

    private function loadAttemptRelations(ExamAttempt $attempt): void
    {
        $attempt->load([
            'answers' => function ($query) {
                $query->orderBy('order_index');
            },
            'answers.question.answers' => function ($query) {
                $query->select('id', 'question_id', 'answer_text');
            },
        ]);
    }
}

