<?php

namespace App\Events;


use App\Models\Grupo;
use App\Models\User;


use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UsuarioAceptoSolicitud
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $grupo;
    public $usuario;

    /**
     * Create a new event instance.
     */
    public function __construct(Grupo $grupo, User $usuario)
    {
        //
        $this->grupo = $grupo;
        $this->usuario = $usuario;
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
