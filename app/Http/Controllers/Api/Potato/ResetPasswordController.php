<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\PasswordEmailRequest;
use App\Http\Requests\Potato\PasswordResetRequest;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    public function email(PasswordEmailRequest $request)
    {
        $credentials = $request->only('email');
        $response = $this->broker()->sendResetLink($credentials);

        if ($response == Password::RESET_LINK_SENT) {
            return response()->json(['message' => trans($response)]);
        }

        throw ValidationException::withMessages([
            'email' => [trans($response)],
        ]);
    }

    public function reset(PasswordResetRequest $request)
    {
        $credentials = $request->only(
            'email',
            'password',
            'password_confirmation',
            'token'
        );

        $response = $this->broker()->reset(
            $credentials,
            function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        if ($response == Password::PASSWORD_RESET) {
            return response()->json(['message' => trans($response)]);
        }

        throw ValidationException::withMessages([
            'email' => [trans($response)]
        ]);
    }

    protected function broker(): PasswordBroker
    {
        return Password::broker();
    }

    protected function resetPassword($user, $password)
    {

    }
}
