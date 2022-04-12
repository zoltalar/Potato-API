<?php

declare(strict_types = 1);

namespace App\Observers;

use App\Models\Category;

class CategoryObserver
{
    public function deleted(Category $category)
    {
        $category->translations()->delete();
    }
}
