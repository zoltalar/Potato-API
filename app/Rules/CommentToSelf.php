<?php

declare(strict_types = 1);

namespace App\Rules;

use App\Models\Base;
use App\Models\Comment;
use App\Models\Review;
use Illuminate\Contracts\Validation\Rule;

class CommentToSelf implements Rule
{
    /** @var string */
    protected $type;

    /** @var int */
    protected $id;

    public function __construct($type, $id)
    {
        $this->type = $type;
        $this->id = $id;
    }

    public function passes($attribute, $value): bool
    {
        $commentable = $this->commentable();

        if ($commentable !== null) {
            $user = $commentable->user;

            return ($user !== null && $user->id != auth()->id());
        }

        return true;
    }

    public function message(): string
    {
        $commentable = $this->commentable();

        if ($commentable instanceof Review) {
            return __('messages.comment_reviews_to_self_error');
        }

        return __('messages.comment_to_self_error');
    }

    protected function commentable(): ?Base
    {
        $type = $this->type;
        $id = $this->id;
        $commentable = null;

        if ($type === Comment::TYPE_COMMENTABLE_REVIEW) {
            $commentable = Review::query()
                ->with(['user'])
                ->find($id);
        }

        return $commentable;
    }
}
