<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Potato\FarmResource;
use App\Http\Resources\Potato\FavoriteResource;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato']);
    }

    public function farms()
    {
        $farms = auth()
            ->user()
            ->farms()
            ->with([
                'images' => function($query) {
                    $query
                        ->orderBy('primary', 'desc')
                        ->orderBy('cover', 'desc')
                        ->orderBy('id', 'asc');
                }
            ])
            ->orderBy('name', 'asc')
            ->get();

        return FarmResource::collection($farms);
    }

    public function favorites()
    {
        $favorites = auth()
            ->user()
            ->favorites()
            ->with(['favoriteable', 'favoriteable.images'])
            ->get();

        return FavoriteResource::collection($favorites);
    }

    public function reviews()
    {
        $reviews = auth()
            ->user()
            ->reviews()
            ->with([
                'rateable' => function($query) {
                    $query->select([
                        'id',
                        'name'
                    ]);
                }
            ])
            ->active()
            ->get();

        return BaseResource::collection($reviews);
    }

    public function messages()
    {
        $messages = auth()
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
            ->orderBy('created_at', 'desc')
            ->get();

        return BaseResource::collection($messages);
    }
}
