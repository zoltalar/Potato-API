<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);

        $query = Language::query()
            ->active()
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query->search(['name', 'native', 'code'], $search);
                });
            })
            ->orders('name', 'asc');

        $languages = ($request->all ? $query->get() : $query->paginate($limit));

        return BaseResource::collection($languages);
    }
}
