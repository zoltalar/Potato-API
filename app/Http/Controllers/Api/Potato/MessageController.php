<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\MessageReplyRequest;
use App\Http\Requests\Potato\MessageStoreRequest;
use App\Http\Resources\MessageResource;
use App\Models\Farm;
use App\Models\Message;
use Exception;

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

    public function reply(MessageReplyRequest $request, Message $reply)
    {
        $message = new Message();
        $message->fill($request->only($message->getFillable()));
        $message->subject = 'Re: ' . $reply->subject;
        $message->reply_id = $reply->id;
        $message->recipient_id = $reply->sender_id;
        $message->save();

        return new MessageResource($message);
    }

    public function show(int $id)
    {
        $message = auth()
            ->user()
            ->receivedMessages()
            ->with([
                'sender' => function($query) {
                    $query->select([
                        'id',
                        'first_name',
                        'last_name'
                    ]);
                }
            ])
            ->find($id);

        if ($message !== null) {

            if (empty($message->read_at)) {
                $message->update(['read_at' => $message->freshTimestamp()]);
            }
        }

        return new MessageResource($message);
    }

    public function destroy(int $id)
    {
        $status = 403;

        $message = auth()
            ->user()
            ->receivedMessages()
            ->find($id);

        if ($message !== null) {

            try {
                if ($message->delete()) {
                    $status = 204;
                }
            } catch (Exception $e) {}
        }

        return response()->json(null, $status);
    }
}
