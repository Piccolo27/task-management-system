<?php

namespace App\Events\DmReply;

use App\Models\DmReply;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DmReplySent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public DmReply $dmReply;

    /**
     * Create a new event instance.
     *
     * @param DmReply $dmReply
     */
    public function __construct(DmReply $dmReply)
    {
        $this->dmReply = $dmReply;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('dm-thread-' . $this->dmReply->thread->dm_thread_id),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'dm-reply-sent';
    }
}
