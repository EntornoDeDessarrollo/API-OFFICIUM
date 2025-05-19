<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\Publicacion;
use App\Models\User;

class PublicacionLiked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $publicacion;
    public $usuarioLike;

    /**
     * Create a new event instance.
     */
    public function __construct(Publicacion $publicacion, User $usuarioLike)
    {
        //
        $this->publicacion = $publicacion;
        $this->usuarioLike = $usuarioLike;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
