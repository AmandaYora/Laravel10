<?php

namespace App\Services;

use App\Models\Attribute as AttributeModel;

class Attribute
{
    protected $attributeModel;

    public function __construct(AttributeModel $attributeModel)
    {
        $this->attributeModel = $attributeModel;
    }

    /**
     * Get the attributes with a custom mapping for a specific user.
     *
     * @param int $userId
     * @return array
     */
    public function getMappedAttributesByUserId($userId)
    {
        $attributes = $this->attributeModel->where('user_id', $userId)->get();
        $mappedAttributes = [];

        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                $mappedAttributes[$attribute->attribute] = [
                    'value' => $attribute->value,
                    'type' => $attribute->type,
                ];
            }
        }

        return $mappedAttributes;
    }
}
