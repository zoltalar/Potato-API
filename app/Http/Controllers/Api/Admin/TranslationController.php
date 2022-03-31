<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TranslationResource;
use App\Models\Translation;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'scope:admin']);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);

        $query = Translation::query()
            ->with(['language'])
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query->search(['name'], $search);
                });
            })
            ->orders('name', 'asc');

        $translations = $query->paginate($limit);

        return TranslationResource::collection($translations);
    }
}
