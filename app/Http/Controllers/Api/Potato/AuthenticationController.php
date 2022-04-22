<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato'])->only(['logout']);
    }

    public function login(Request $request)
    {
        $user = User::query()
            ->where('email', $request->email)
            ->active()
            ->first();

        if ($user !== null) {

            if (Hash::check($request->password, $user->password)) {

                $result = $user->createToken('Potato API Token', ['potato']);

                $token = $result->token;
                $token->expires_at = Carbon::now()->addHours(8);

                if ($token->save()) {

                    return response()->json([
                        'token' => $result->accessToken,
                        'type' => 'Bearer',
                        'expires_at' => $token->expires_at->toDateTimeString()
                    ]);
                }
            }
        }

        return response()->json(['error' => 'Unauthorized']);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
