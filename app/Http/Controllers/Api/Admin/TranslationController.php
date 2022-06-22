<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TranslationStoreRequest;
use App\Http\Requests\Admin\TranslationUpdateRequest;
use App\Http\Resources\BaseResource;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Language;
use App\Models\Translation;
use Exception;
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
            ->with([
                'language',
                'translatable' => function($morphTo) {
                    $morphTo->morphWith([Category::class, Inventory::class]);
                }
            ])
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query->search(['name'], $search);
                });
            })
            ->orders('name', 'asc');

        $translations = $query->paginate($limit);

        return BaseResource::collection($translations);
    }

    public function store(TranslationStoreRequest $request)
    {
        $translation = null;
        $name = $request->name;
        $language = Language::find($request->language_id);
        $translatable = null;
        $type = $request->translatable_type;
        $id = $request->translatable_id;

        if ($type == Translation::TYPE_CATEGORY) {
            $translatable = Category::find($id);
        } elseif ($type == Translation::TYPE_INVENTORY) {
            $translatable = Inventory::find($id);
        }

        if ($language !== null && $translatable !== null) {
            $attributes = [
                'name' => $name,
                'language_id' => $language->id
            ];

            $translation = $translatable->translations()->save(new Translation($attributes));
        }

        return new BaseResource($translation);
    }

    public function update(TranslationUpdateRequest $request, Translation $translation)
    {
        $translation->fill($request->only($translation->getFillable()));
        $translation->update();

        return new BaseResource($translation);
    }

    public function destroy(Translation $translation)
    {
        $status = 403;

        try {
            if ($translation->delete(true)) {
                $status = 204;
            }
        } catch (Exception $e) {}

        return response()->json(null, $status);
    }

    public function meta()
    {
        return response()->json(['types' => Translation::types()]);
    }
}
