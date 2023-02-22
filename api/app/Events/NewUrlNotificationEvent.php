<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewUrlNotificationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    /**
     * Create a new event instance.
     */
    public function __construct(User $creator)
    {
        $this->data = [
            'receivers' => $creator->followers->pluck('email'),
            'text' => 'A user you follow has published a new url'
        ];
    }
}
