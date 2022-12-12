<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Models\Comment;
use Exception;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'scope:admin']);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);

        $query = Comment::query()
            ->with([
                'commentable',
                'user'
            ])
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query->whereHas('commentable', function ($query) use ($search) {
                        $query->search(['title'], $search);
                    });
                });
            })
            ->orders('id', 'desc');

        $comments = $query->paginate($limit);

        return BaseResource::collection($comments);
    }

    public function destroy(Comment $comment)
    {
        $status = 403;

        try {
            if ($comment->delete(true)) {
                $status = 204;
            }
        } catch (Exception $e) {}

        return response()->json(null, $status);
    }
}
