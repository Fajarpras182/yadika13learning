<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentHeartbeatReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $studentId;
    public $status;
    public $examSessionId;

    /**
     * Create a new event instance.
     */
    public function __construct(int $examSessionId, int $studentId, string $status = 'online')
    {
        $this->examSessionId = $examSessionId;
        $this->studentId = $studentId;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast ke channel spesifik untuk sesi ujian ini
        return [
            new Channel('exam-session.'.$this->examSessionId),
        ];
    }
}
