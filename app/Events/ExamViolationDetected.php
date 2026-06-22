<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExamViolationDetected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $studentId;
    public $violationType;
    public $violationsCount;
    public $examSessionId;

    /**
     * Create a new event instance.
     */
    public function __construct(int $examSessionId, int $studentId, string $violationType, int $violationsCount)
    {
        $this->examSessionId = $examSessionId;
        $this->studentId = $studentId;
        $this->violationType = $violationType;
        $this->violationsCount = $violationsCount;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('exam-session.'.$this->examSessionId),
        ];
    }
}
