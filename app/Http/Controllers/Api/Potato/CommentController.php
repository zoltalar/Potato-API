<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\CommentStoreRequest;
use App\Http\Resources\BaseResource;
use App\Models\Comment;
use App\Models\Review;

class CommentController extends Controller
{
    public function __construct()
    {
        $this
            ->middleware(['auth:user', 'scope:potato'])
            ->only(['store']);
    }

    public function store(CommentStoreRequest $request, string $type, int $id)
    {
        $comment = $commentable = null;

        if ($type === Comment::TYPE_COMMENTABLE_REVIEW) {
            $commentable = Review::query()
                ->with(['user'])
                ->find($id);
        }

        if ($commentable !== null) {
            $comment = new Comment();
            $comment->fill($request->only($comment->getFillable()));
            $comment->user_id = auth()->id();

            $commentable->comments()->save($comment);
        }

        return new BaseResource($comment);
    }
}
