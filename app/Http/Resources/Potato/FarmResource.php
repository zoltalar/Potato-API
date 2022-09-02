<?php

declare(strict_types = 1);

namespace App\Http\Resources\Potato;

use App\Http\Resources\BaseResource;
use App\Models\Address;
use Illuminate\Http\Resources\Json\JsonResource;

class FarmResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->when($this->publishPhone(), function() {
                return $this->phone;
            }),
            'publish_phone' => $this->publish_phone,
            'publish_address' => $this->publish_address,
            'publish_mailing_address' => $this->publish_mailing_address,
            'fax' => $this->fax,
            'email' => $this->when(
                auth()->check() && auth()->id() === $this->user_id,
                function() {
                    return $this->email;
                }
            ),
            'website' => $this->website,
            'description' => $this->description,
            'facebook' => $this->facebook,
            'twitter' => $this->twitter,
            'pinterest' => $this->pinterest,
            'instagram' => $this->instagram,
            'promote' => $this->promote,
            'active' => $this->active,
            'user_id' => $this->user_id,
            'average_rating' => $this->average_rating,
            'reviews_count' => $this->reviews_count,
            'addresses' => $this->when(
                $this->relationLoaded('addresses'),
                function() {
                    $addresses = $this
                        ->addresses
                        ->when( ! $this->publishAddress(), function($addresses, $address) {
                            return $addresses->reject(function($address) {
                                return $address->type == Address::TYPE_LOCATION;
                            });
                        })
                        ->when( ! $this->publishMailingAddress(), function ($addresses, $address) {
                            return $addresses->reject(function($address) {
                                return $address->type == Address::TYPE_MAILING;
                            });
                        });
                    return BaseResource::collection($addresses);
                }
            ),
            'images' => $this->when(
                $this->relationLoaded('images'),
                function() {
                    return BaseResource::collection($this->images);
                }
            ),
            'operating_hours' => $this->when(
                $this->relationLoaded('operatingHours'),
                function() {
                    return new BaseResource($this->operatingHours);
                }
            ),
            'products' => $this->when(
                $this->relationLoaded('products'),
                function() {
                    return BaseResource::collection($this->products);
                }
            ),
            'reviews' => $this->when(
                $this->relationLoaded('reviews'),
                function() {
                    return BaseResource::collection($this->reviews);
                }
            )
        ];
    }
}
