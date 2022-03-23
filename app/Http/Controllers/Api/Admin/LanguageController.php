<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LanguageStoreRequest;
use App\Http\Requests\Admin\LanguageUpdateRequest;
use App\Http\Resources\LanguageResource;
use App\Models\Language;
use Exception;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'scope:admin']);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);

        $query = Language::query()
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query->search(['name', 'native', 'code'], $search);
                });
            })
            ->orders('id', 'desc');

        $languages = ($request->all ? $query->get() : $query->paginate($limit));

        return LanguageResource::collection($languages);
    }

    public function store(LanguageStoreRequest $request)
    {
        $language = new Language();
        $language->fill($request->only($language->getFillable()));
        $language->save();

        return new LanguageResource($language);
    }

    public function update(LanguageUpdateRequest $request, Language $language)
    {
        $language->fill($request->only($language->getFillable()));
        $language->update();

        return new LanguageResource($language);
    }

    public function destroy(Language $language)
    {
        $status = 403;

        try {
            if ($language->delete(true)) {
                $status = 204;
            }
        } catch (Exception $e) {}

        return response()->json(null, $status);
    }
}
