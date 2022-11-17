<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Models\VerificationCode;
use Hash;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato']);
        $this->middleware(['throttle:5,1']);
    }

    public function verify(Request $request, string $code)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([], 204);
        }

        $valid = VerificationCode::query()
            ->where('verifiable', $user->getEmailForVerification())
            ->notExpired()
            ->cursor()
            ->contains(function($verificationCode) use ($code) {
                return Hash::check($code, $verificationCode->code);
            });

        if ($valid) {

            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }

            VerificationCode::query()
                ->where('verifiable', $user->getEmailForVerification())
                ->delete();

            return response()->json([], 204);
        }

        return response()->json(['error' => 'Unverified']);
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
