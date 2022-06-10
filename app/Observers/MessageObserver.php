<?php

declare(strict_types = 1);

namespace App\Observers;

use App\Models\Message;

class MessageObserver
{
    public function creating(Message $message)
    {
        if (empty($message->sender_id)) {
            $message->sender_id = auth()->id();
        }
    }
}
