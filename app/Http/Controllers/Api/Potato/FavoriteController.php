<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Models\Farm;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato']);
    }

    public function store(string $type, int $id)
    {
        $favorite = null;
        $favoriteable = null;

        if ($type === Favorite::TYPE_FAVORITEABLE_FARM) {
            $favoriteable = Farm::find($id);
        }

        if ($favoriteable !== null) {
            $user = auth()->user();
            $favorite = $favoriteable->favorite($user);

            if ($favoriteable->user_id !== $user->id && $favorite === null) {
                $favorite = $favoriteable->favorites()->save(new Favorite(['user_id' => $user->id]));
            }
        }

        return new BaseResource($favorite);
    }
}
