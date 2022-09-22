<?php

declare(strict_types = 1);

namespace App\Rules;

use App\Models\Farm;
use App\Models\Market;
use App\Models\Message;
use Illuminate\Contracts\Validation\Rule;

class MessageToSelf implements Rule
{
    /** @var string */
    protected $type;

    /** @var int */
    protected $id;

    public function __construct($type, $id)
    {
        $this->type = $type;
        $this->id = $id;
    }

    public function passes($attribute, $value): bool
    {
        $type = $this->type;
        $id = $this->id;
        $messageable = null;

        if ($type === Message::TYPE_MESSAGEABLE_FARM) {
            $messageable = Farm::find($id);
        } elseif ($type === Message::TYPE_MESSAGEABLE_MARKET) {
            $messageable = Market::find($id);
        }

        return ($messageable !== null && $messageable->user_id != auth()->id());
    }

    public function message(): string
    {
        return __('messages.message_to_self_error');
    }
}
