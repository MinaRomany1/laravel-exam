<?php

namespace App\Http\Resources\Api\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            $this->mergeWhen($request->routeIs('exams.show'), fn() => [
                'description' => $this->description,
            ]),
            'questions_count' => $this->questions_count,
            'category' => $this->whenLoaded('category', fn() => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'multiple_attempts' => (bool) $this->multiple_attempts,
            'pass_percentage' => $this->pass_percentage,
            'show_results' => (bool) $this->show_results,
            'show_detailed_result' => (bool) $this->show_detailed_result,
            $this->mergeWhen($request->routeIs('exams.show'), fn() => [
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'duration_minutes' => $this->duration_minutes,
                'enable_image_answers' => (bool) $this->enable_image_answers,
                'has_session' => $this->resolveHasSession($request),
                'is_session_active' => (bool) $this->resolveActiveSession($request),
            ]),
        ];
    }

    private function resolveActiveSession(Request $request): mixed
    {
        $user = $request->user();

        if (!$user) {
            return null;
        }

        return $this->sessions()
            ->where('user_id', $user->id)
            ->where('active', true)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now()))
            ->latest('id')
            ->first();
    }

    private function resolveHasSession(Request $request): bool
    {
        $user = $request->user();

        return $user
            ? $this->sessions()->where('user_id', $user->id)->exists()
            : false;
    }
}
