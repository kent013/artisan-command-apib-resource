<?php declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\SomeObject;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SomeObject
 */
class SomeObjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'language' => $this->language->value,
            'region' => $this->region->value,
            'createtd_at' => $this->created_at->toIso8601String(),
            'detail' => new SomeObjectDetailResource($this->detail),
            'details' => SomeObjectDetailResource::collection($this->details),
        ];
    }
}
