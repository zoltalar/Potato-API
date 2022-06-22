<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'scope:admin']);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);

        $query = Message::query()
            ->with([
                'sender' => function($query) {
                    $query->select([
                        'id',
                        'first_name',
                        'last_name'
                    ]);
                },
                'recipient' => function($query) {
                    $query->select([
                        'id',
                        'first_name',
                        'last_name'
                    ]);
                }
            ])
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query
                        ->search(['subject'], $search)
                        ->orWhereHas('sender', function($query) use ($search) {
                            $query->search(['first_name', 'last_name', 'email'], $search);
                        })
                        ->orWhereHas('recipient', function($query) use ($search) {
                            $query->search(['first_name', 'last_name', 'email'], $search);
                        });
                });
            })
            ->orders('id', 'desc');

        $messages = $query->paginate($limit);

        return BaseResource::collection($messages);
    }
}
