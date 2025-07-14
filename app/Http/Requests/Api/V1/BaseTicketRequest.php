<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseTicketRequest extends FormRequest
{

    public function mappedAttributes(?int $overrideUserId = null)
    {
        $attributeMap = [
            'data.attributes.title' => 'title',
            'data.attributes.description' => 'description',
            'data.attributes.status' => 'status',
            'data.attributes.createdAt' => 'created_at',
            'data.attributes.updatedAt' => 'updated_at',
            'data.relationships.author.data.id' => 'user_id'
        ];

        $attributesToUpdate = [];

        foreach ($attributeMap as $key => $attribute) {
            if($this->has($key)) {
                $attributesToUpdate[$attribute] = $this->input($key);
            }
        }

        // Override user_id if provided
        if ($overrideUserId !== null) {
            $attributesToUpdate['user_id'] = $overrideUserId;
        }

        return $attributesToUpdate;
    }
    public function messages()
    {
        return [
        'data.attributes.status' => 'the data.attributes.status value is invalid. please use A,C,H, or X.'
        ];
    }
}
