<?php

declare(strict_types = 1);

namespace App\Observers;

use App\Models\Message;
use Str;

class MessageObserver
{
    public function creating(Message $message)
    {
        if (empty($message->sender_id)) {
            $message->sender_id = auth()->id();
        }
    }

    public function saving(Message $message)
    {
        if (empty($message->token)) {
            $message->token = Str::uuid()->getHex()->toString();
        }
    }
}
