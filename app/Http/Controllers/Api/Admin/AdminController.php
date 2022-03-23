<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminStoreRequest;
use App\Http\Requests\Admin\AdminUpdateRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use Exception;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'scope:admin']);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);

        $query = Admin::query()
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query->search(['first_name', 'last_name', 'email', 'phone'], $search);
                });
            })
            ->orders('id', 'desc');

        $admins = $query->paginate($limit);

        return AdminResource::collection($admins);
    }

    public function store(AdminStoreRequest $request)
    {
        $admin = new Admin();
        $admin->fill($request->only($admin->getFillable()));
        $admin->save();

        return new AdminResource($admin);
    }

    public function update(AdminUpdateRequest $request, Admin $admin)
    {
        $data = $request->only($admin->getFillable());

        if (empty($request->password)) {
            unset($data['password']);
        }

        $admin->fill($data);
        $admin->update();

        return new AdminResource($admin);
    }

    public function destroy(Admin $admin)
    {
        $status = 403;

        if (auth()->id() != $admin->id) {
            try {
                if ($admin->delete(true)) {
                    $status = 204;
                }
            } catch (Exception $e) {}
        }

        return response()->json(null, $status);
    }

    public function current()
    {
        return new AdminResource(auth()->user());
    }
}
