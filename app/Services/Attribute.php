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

    public function saveAttribute($userId, $attributeName, $value, $type)
    {
        try {
            $attribute = $this->attributeModel->where('user_id', $userId)
                                            ->where('attribute', $attributeName)
                                            ->first();

            if ($attribute) {
                $attribute->value = $value;
                $attribute->type = $type;
                $attribute->save();
            } else {
                $this->attributeModel->create([
                    'user_id' => $userId,
                    'attribute' => $attributeName,
                    'value' => $value,
                    'type' => $type,
                ]);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function saveAttr($userId, array $data, $mode)
    {
        if($mode == Person::MODE_ID){
            unset($data['user_id']);
        }

        foreach ($data as $attributeName => $value) {
            $type = gettype($value);

            $result = $this->saveAttribute($userId, $attributeName, $value, $type);
            if (!$result) {
                return false;
            }
        }

        return true;
    }

}
