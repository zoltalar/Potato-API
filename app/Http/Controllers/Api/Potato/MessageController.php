<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\MessageReplyRequest;
use App\Http\Requests\Potato\MessageStoreRequest;
use App\Http\Resources\BaseResource;
use App\Models\Farm;
use App\Models\Message;
use Exception;
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

        return new BaseResource($message);
    }

    public function reply(MessageReplyRequest $request, string $token)
    {
        $message = null;

        $reply = Message::query()
            ->where('token', $token)
            ->first();

        if ($reply !== null) {
            $message = new Message();
            $message->fill($request->only($message->getFillable()));
            $message->subject = $reply->replySubject();
            $message->reply_id = $reply->id;
            $message->recipient_id = $reply->sender_id;
            $message->save();
        }

        return new BaseResource($message);
    }

    public function show(string $token)
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
            ->where('token', $token)
            ->first();

        if ($message !== null) {

            if (empty($message->read_at)) {
                $message->update(['read_at' => $message->freshTimestamp()]);
            }
        }

        return new BaseResource($message);
    }

    public function destroy(string $token)
    {
        $status = 403;

        $message = auth()
            ->user()
            ->receivedMessages()
            ->where('token', $token)
            ->first();

        if ($message !== null) {

            try {
                if ($message->delete()) {
                    $status = 204;
                }
            } catch (Exception $e) {}
        }

        return response()->json(null, $status);
    }

    public function destroyBatch(Request $request)
    {
        $status = 403;
        $ids = $request->get('ids', []);

        if (count($ids) > 0) {
            $messages = auth()
                ->user()
                ->receivedMessages()
                ->findMany($ids);

            if (count($messages) > 0) {
                $count = Message::destroy($messages->pluck('id')->toArray());

                if ($count > 0) {
                    $status = 204;
                }
            }
        }

        return response()->json(null, $status);
    }
}
