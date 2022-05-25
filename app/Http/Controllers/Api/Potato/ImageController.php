<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\ImageStoreRequest;
use App\Http\Resources\ImageResource;
use App\Models\Farm;
use App\Models\Image;
use App\Models\Inventory;
use Str;

class ImageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato']);
    }

    public function store(ImageStoreRequest $request, string $type, int $id)
    {
        $image = null;
        $imageable = null;

        if ($type === Image::TYPE_IMAGEABLE_FARM) {
            $imageable = Farm::query()
                ->where('user_id', auth()->id())
                ->find($id);
        }

        if ($imageable !== null) {
            $image = new Image();
            $image->fill($request->only($image->getFillable()));

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $name = Inventory::randomFileName() . '.' . $file->guessExtension();
                $directory = Str::plural($type);

                $file->storeAs($directory, $name, 'public');
                $image->file = $name;
            }

            $imageable->images()->save($image);
        }

        return new ImageResource($image);
    }
}
