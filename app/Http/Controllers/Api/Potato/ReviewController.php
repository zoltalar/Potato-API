<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\ReviewStoreRequest;
use App\Http\Resources\BaseResource;
use App\Models\Farm;
use App\Models\Language;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(ReviewStoreRequest $request, string $type, int $id)
    {
        $review = $rateable = null;

        if ($type === Review::TYPE_RATEABLE_FARM) {
            $rateable = Farm::find($id);
        }

        if ($rateable !== null) {
            $review = new Review();
            $review->fill($request->only($review->getFillable()));
            $review->user_id = auth()->id();
            $review->active = 1;

            $rateable->reviews()->save($review);
        }

        return new BaseResource($review);
    }
}
