<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\MessageStoreRequest;
use App\Http\Resources\CityResource;
use App\Http\Resources\MessageResource;
use App\Models\City;
use App\Models\Country;
use App\Models\Farm;
use App\Models\Message;
use App\Models\Unit;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato']);
    }

    public function store(MessageStoreRequest $request, string $type, int $id)
    {
        $message = $messageable = null;

        if ($type === Message::TYPE_MESSAGEABLE_FARM) {
            $messageable = Farm::find($id);
        }

        if ($messageable !== null) {
            $message = new Message();
            $message->fill($request->only($message->getFillable()));
            $message->recipient_id = $messageable->user_id;
            $message->save();
        }

        return new MessageResource($message);
    }
}
