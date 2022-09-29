<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\PasswordResetRequest;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    public function reset(PasswordResetRequest $request)
    {
        $response = $this->broker()->sendResetLink($request->only('email'));

        if ($response == Password::RESET_LINK_SENT) {
            return response()->json(['message' => trans($response)]);
        }

        throw ValidationException::withMessages([
            'email' => [trans($response)],
        ]);
    }

    protected function broker(): PasswordBroker
    {
        return Password::broker();
    }
}
