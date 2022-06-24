<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LanguageStoreRequest;
use App\Http\Requests\Admin\LanguageUpdateRequest;
use App\Http\Resources\BaseResource;
use App\Models\Language;
use App\Models\Review;
use Exception;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'scope:admin']);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);

        $query = Review::query()
            ->with([
                'rateable' => function($query) {
                    $query->select([
                        'id',
                        'name'
                    ]);
                },
                'user'
            ])
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query->search(['title'], $search);
                });
            })
            ->orders('id', 'desc');

        $reviews = $query->paginate($limit);

        return BaseResource::collection($reviews);
    }

    public function activate(Review $review)
    {
        $review->active = 1;
        $review->update();

        return new BaseResource($review);
    }

    public function deactivate(Review $review)
    {
        $review->active = 0;
        $review->update();

        return new BaseResource($review);
    }
}
