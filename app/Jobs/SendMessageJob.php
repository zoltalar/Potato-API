<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\NewMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    /** @var User */
    protected $sender;

    /** @var User */
    protected $recipient;

    public function __construct(User $sender, User $recipient)
    {
        $this->sender = $sender;
        $this->recipient = $recipient;
    }

    public function handle()
    {
        $sender = $this->sender;
        $recipient = $this->recipient;

        $recipient->notify(new NewMessage($sender, $recipient));
    }
}
