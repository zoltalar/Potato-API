<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'scope:admin'])->only(['logout']);
    }

    public function login(Request $request)
    {
        $admin = Admin::query()
            ->where('email', $request->email)
            ->active()
            ->first();

        if ($admin !== null) {

            if (Hash::check($request->password, $admin->password)) {

                $result = $admin->createToken('Potato Admin API Token', ['admin']);

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
