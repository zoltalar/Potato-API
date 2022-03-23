<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'scope:admin']);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);

        $query = User::query()
            ->with([
                'country',
                'language'
            ])
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query->search(['first_name', 'last_name', 'email', 'phone'], $search);
                });
            })
            ->orders('id', 'desc');

        $admins = $query->paginate($limit);

        return UserResource::collection($admins);
    }

    public function store(UserStoreRequest $request)
    {
        $user = new User();
        $user->fill($request->only($user->getFillable()));
        $user->save();

        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $data = $request->only($user->getFillable());

        if (empty($request->password)) {
            unset($data['password']);
        }

        $user->fill($data);
        $user->update();

        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $status = 403;

        try {
            if ($user->delete(true)) {
                $status = 204;
            }
        } catch (Exception $e) {}

        return response()->json(null, $status);
    }
}
