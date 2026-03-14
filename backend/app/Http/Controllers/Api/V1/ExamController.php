<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\Api\V1\StoreExamRequest;
use App\Http\Requests\Api\V1\UpdateExamRequest;
use App\Models\Exam;
use App\Http\Resources\Api\V1\ExamResource;
use App\Http\Resources\Api\V1\ExamStartResource;
use App\Services\ExamSessionService;
use App\Http\Controllers\Controller;

class ExamController extends Controller
{
    public function __construct(
        private readonly ExamSessionService $examSessionService,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $now = Carbon::now();

        $exams = Exam::query()
            ->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_time')
                    ->orWhere('start_time', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_time')
                    ->orWhere('end_time', '>=', $now);
            })
            ->with('category')
            ->withCount('questions')
            ->orderBy('start_time')
            ->orderBy('id')
            ->get();

        return ExamResource::collection($exams);
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, Exam $exam)
    {
        $exam->load('category')->loadCount('questions');

        return new ExamResource($exam);
    }

    /**
     * Start an exam: create or reuse a session and attempt,
     * and materialize the question order on exam_attempt_answers.
     */
    public function start(Request $request, Exam $exam)
    {
        $user = $request->user();

        if (!$exam->isAvailable()) {
            return response()->json([
                'message' => 'Exam is not available.',
            ], 403);
        }

        $sessionData = $this->examSessionService->startExam($exam, $user);

        $statusCode = $sessionData['status_code'] ?? 200;

        return (new ExamStartResource($sessionData))
            ->response()
            ->setStatusCode($statusCode);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExamRequest $request, Exam $exam)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        //
    }
}
