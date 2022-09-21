<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\ImageStoreRequest;
use App\Http\Resources\BaseResource;
use App\Models\Image;
use App\Models\Inventory;
use Illuminate\Http\Request;
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
            $imageable = auth()
                ->user()
                ->farms()
                ->find($id);
        } elseif ($type === Image::TYPE_IMAGEABLE_MARKET) {
            $imageable = auth()
                ->user()
                ->markets()
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

        return new BaseResource($image);
    }

    public function update(Request $request, int $id, string $type, int $imageableId)
    {
        $image = null;
        $imageable = null;

        if ($type === Image::TYPE_IMAGEABLE_FARM) {
            $imageable = auth()
                ->user()
                ->farms()
                ->with(['images'])
                ->find($imageableId);
        } elseif ($type === Image::TYPE_IMAGEABLE_MARKET) {
            $imageable = auth()
                ->user()
                ->markets()
                ->with(['images'])
                ->find($imageableId);
        }

        if ($imageable !== null) {

            if ($imageable->images->contains('id', $id)) {
                $image = $imageable
                    ->images
                    ->filter(function($image) use ($id) {
                        return $image->getKey() === $id;
                    })
                    ->first();

                if ($image !== null) {
                    $image->fill($request->only($image->getFillable()));
                    $image->update();
                }
            }
        }

        return new BaseResource($image);
    }

    public function updateCover(int $id, string $type, int $imageableId)
    {
        $image = null;
        $imageable = null;

        if ($type === Image::TYPE_IMAGEABLE_FARM) {
            $imageable = auth()
                ->user()
                ->farms()
                ->with(['images'])
                ->find($imageableId);
        } elseif ($type === Image::TYPE_IMAGEABLE_MARKET) {
            $imageable = auth()
                ->user()
                ->markets()
                ->with(['images'])
                ->find($imageableId);
        }

        if ($imageable !== null) {

            if ($imageable->images->contains('id', $id)) {
                $image = $imageable
                    ->images
                    ->filter(function($image) use ($id) {
                        return $image->getKey() === $id;
                    })
                    ->first();

                if ($image !== null) {
                    $image->primary = 0;
                    $image->cover = 1;

                    if ($image->update()) {
                        $imageable
                            ->images()
                            ->where('id', '!=', $image->id)
                            ->update(['cover' => 0]);
                    }
                }
            }
        }

        return new BaseResource($image);
    }

    public function updatePrimary(int $id, string $type, int $imageableId)
    {
        $image = null;
        $imageable = null;

        if ($type === Image::TYPE_IMAGEABLE_FARM) {
            $imageable = auth()
                ->user()
                ->farms()
                ->with(['images'])
                ->find($imageableId);
        } elseif ($type === Image::TYPE_IMAGEABLE_MARKET) {
            $imageable = auth()
                ->user()
                ->markets()
                ->with(['images'])
                ->find($imageableId);
        }

        if ($imageable !== null) {

            if ($imageable->images->contains('id', $id)) {
                $image = $imageable
                    ->images
                    ->filter(function($image) use ($id) {
                        return $image->getKey() === $id;
                    })
                    ->first();

                if ($image !== null) {
                    $image->primary = 1;
                    $image->cover = 0;

                    if ($image->update()) {
                        $imageable
                            ->images()
                            ->where('id', '!=', $image->id)
                            ->update(['primary' => 0]);
                    }
                }
            }
        }

        return new BaseResource($image);
    }

    public function destroy(int $id, string $type, int $imageableId)
    {
        $status = 403;
        $imageable = null;

        if ($type === Image::TYPE_IMAGEABLE_FARM) {
            $imageable = auth()
                ->user()
                ->farms()
                ->with(['images'])
                ->find($imageableId);
        } elseif ($type === Image::TYPE_IMAGEABLE_MARKET) {
            $imageable = auth()
                ->user()
                ->markets()
                ->with(['images'])
                ->find($imageableId);
        }

        if ($imageable !== null) {

            if ($imageable->images->contains('id', $id)) {
                $image = $imageable
                    ->images
                    ->filter(function($image) use ($id) {
                        return $image->getKey() === $id;
                    })
                    ->first();

                if ($image !== null) {

                    if ($image->delete()) {
                        $status = 204;
                    }
                }
            }
        }

        return response()->json(null, $status);
    }
}
