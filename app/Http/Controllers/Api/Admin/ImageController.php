<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Models\Image;
use Exception;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'scope:admin']);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 9);

        $query = Image::query()
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query->search(['title'], $search);
                });
            })
            ->orders('id', 'desc');

        $images = $query->paginate($limit);

        return BaseResource::collection($images);
    }

    public function destroy(Image $image)
    {
        $status = 403;

        try {
            if ($image->delete()) {
                $status = 204;
            }
        } catch (Exception $e) {}

        return response()->json(null, $status);
    }
}
