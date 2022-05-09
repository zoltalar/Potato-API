<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato']);
        $this->middleware(['throttle:5,1']);
    }

    public function verify(Request $request, int $id, string $email)
    {
        $user = $request->user();

        try {
            $email = decrypt($email);
        } catch (DecryptException $e) {}

        if ($user->getKey() !== $id) {
            throw new AuthorizationException;
        }

        if ($user->getEmailForVerification() !== $email) {
            throw new AuthorizationException;
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([], 204);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json([], 204);
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([], 204);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([], 202);
    }
}
