<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Country;
use App\Models\Language;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this
            ->middleware(['auth:user', 'scope:potato'])
            ->only([
                'current',
                'updateCountry',
                'updateLanguage'
            ]);
    }

    public function current()
    {
        $user = User::find(auth()->id());

        return new UserResource($user);
    }

    public function updateCountry(Request $request)
    {
        $user = auth()->user();

        if ($user !== null) {
            $country = Country::query()
                ->where('code', $request->code)
                ->first();

            if ($country !== null) {
                $user->country_id = $country->id;
                $user->save();
            }
        }

        return new UserResource($user);
    }

    public function updateLanguage(Request $request)
    {
        $user = auth()->user();

        if ($user !== null) {
            $language = Language::query()
                ->where('code', $request->code)
                ->first();

            if ($language !== null) {
                $user->language_id = $language->id;
                $user->save();
            }
        }

        return new UserResource($user);
    }
}
