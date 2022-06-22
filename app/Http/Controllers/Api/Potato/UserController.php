<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\UserContactInformationUpdateRequest;
use App\Http\Requests\Potato\UserPasswordUpdateRequest;
use App\Http\Resources\BaseResource;
use App\Models\Country;
use App\Models\Language;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato']);
    }

    public function current()
    {
        $user = User::find(auth()->id());

        return new BaseResource($user);
    }

    public function updateContactInformation(UserContactInformationUpdateRequest $request)
    {
        $user = auth()->user();
        $user->update($request->only($user->getFillable()));

        return new BaseResource($user);
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

        return new BaseResource($user);
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

        return new BaseResource($user);
    }

    public function updatePassword(UserPasswordUpdateRequest $request)
    {
        $user = auth()->user();
        $user->update(['password' => $request->password]);

        return new BaseResource($user);
    }
}
