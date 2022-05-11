<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendEmailVerificationJob;
use App\Models\User;
use Carbon\Carbon;
use Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = new User();
        $user->fill($request->only($user->getFillable()));
        $user->active = 1;

        if ($user->save()) {

            if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
                $this->dispatch(new SendEmailVerificationJob($user));
            }
        }

        return new UserResource($user);
    }
}
