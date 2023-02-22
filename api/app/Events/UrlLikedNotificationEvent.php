<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UrlLikedNotificationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    /**
     * Create a new event instance.
     */
    public function __construct(User $receiver)
    {
        $this->data = [
            'receivers' => collect($receiver->email),
            'text' => 'A url you publiched has received a new like'
        ];
    }
}
