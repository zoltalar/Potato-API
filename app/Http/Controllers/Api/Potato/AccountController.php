<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Resources\FarmResource;

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
}
