<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamStartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $session = $this->resource['session'] ?? null;
        $exam = $this->resource['exam'] ?? null;
        $attempt = $this->resource['attempt'] ?? null;

        $questions = [];

        if ($attempt && $attempt->relationLoaded('answers')) {
            $questions = $attempt->answers->map(function ($answer) {
                $question = $answer->question;

                return [
                    'id' => $question->id,
                    'question_text' => $question->question_text,
                    'question_type' => $question->question_type,
                    'image_url' => $question->image_url,
                    'order_index' => $answer->order_index,
                    'answers' => $question->answers->map(function ($ans) {
                        return [
                            'id' => $ans->id,
                            'answer_text' => $ans->answer_text,
                        ];
                    }),
                ];
            })->values();
        }

        return [
            'session' => $session ? [
                'id' => $session->id,
                'token' => $session->token,
                'expires_at' => $session->expires_at,
            ] : null,
            'exam' => $exam ? [
                'id' => $exam->id,
                'name' => $exam->name,
                'description' => $exam->description,
                'start_time' => $exam->start_time,
                'end_time' => $exam->end_time,
                'duration_minutes' => $exam->duration_minutes,
                'pass_percentage' => $exam->pass_percentage,
                'multiple_attempts' => (bool) $exam->multiple_attempts,
                'show_results' => (bool) $exam->show_results,
                'show_detailed_result' => (bool) $exam->show_detailed_result,
                'enable_image_answers' => (bool) $exam->enable_image_answers,
                'category' => $exam->relationLoaded('category') && $exam->category ? [
                    'id' => $exam->category->id,
                    'name' => $exam->category->name,
                    'slug' => $exam->category->slug,
                ] : null,
                'questions' => $questions,
            ] : null,
        ];
    }
}

