<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShortUrlResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'short_url'     => url('/' . $this->short_code),
            'original_url'  => $this->original_url,
            'short_code'    => $this->short_code,
            'created_at'    => $this->created_at,
        ];
    }
}
