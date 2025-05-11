<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TranslationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'key' => $this->key,
            'value' => $this->value,
            'device_type' => $this->device_type,
            'group' => $this->group,
            'is_active' => $this->is_active,
        ];

        // If this is a model with relationships
        if (method_exists($this->resource, 'locale')) {
            $data['locale'] = [
                'id' => $this->locale->id,
                'code' => $this->locale->code,
                'name' => $this->locale->name,
            ];
            $data['created_at'] = $this->created_at;
            $data['updated_at'] = $this->updated_at;
        } else {
            // If this is a DTO
            $data['locale_id'] = $this->locale_id;
        }

        return $data;
    }
} 