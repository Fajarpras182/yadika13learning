<?php

namespace App\Jobs;

use App\Services\Exam\ExamGradingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GradeExamJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private int $examSessionId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(ExamGradingService $gradingService): void
    {
        $gradingService->gradeSession($this->examSessionId);
    }
}
