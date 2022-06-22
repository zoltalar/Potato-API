<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryStoreRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Http\Resources\BaseResource;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'scope:admin']);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);

        $query = Category::query()
            ->withCount('translations')
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query->search(['name'], $search);
                });
            })
            ->orders('name', 'asc');

        $categories = ($request->all ? $query->get() : $query->paginate($limit));

        return BaseResource::collection($categories);
    }

    public function store(CategoryStoreRequest $request)
    {
        $category = new Category();
        $category->fill($request->only($category->getFillable()));
        $category->save();

        return new BaseResource($category);
    }

    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $category->fill($request->only($category->getFillable()));
        $category->update();

        return new BaseResource($category);
    }

    public function destroy(Category $category)
    {
        $status = 403;

        try {
            if ($category->delete(true)) {
                $status = 204;
            }
        } catch (Exception $e) {}

        return response()->json(null, $status);
    }

    public function meta()
    {
        return response()->json(['types' => Category::types()]);
    }
}
