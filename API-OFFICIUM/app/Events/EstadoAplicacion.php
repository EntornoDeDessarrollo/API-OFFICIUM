<?php

namespace App\Events;


use App\Models\Aplicacion;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EstadoAplicacion
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $aplicacion;
    /**
     * Create a new event instance.
     */
    public function __construct(Aplicacion $aplicacion)
    {
        //
        $this->aplicacion = $aplicacion;
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
