<?php

namespace SmartCms\Options\Events;

use SmartCms\Options\Models\Option;
use SmartCms\Options\Models\OptionValue;

class CartItemTransform
{
    public function __invoke(&$dto, $resource)
    {
        $existedOptions = $resource->extra['options'] ?? [];
        $data = [];
        foreach ($existedOptions as $key => $val) {
            $optionModel = Option::query()->find($key);
            if (!$optionModel) {
                continue;
            }
            $optionValue = OptionValue::query()->where('id', $val)->first();
            $data[] = (object) [
                'name' => $optionModel->name(),
                'value' => $optionValue->name() ?? ' - ',
            ];
        }
        $dto['options'] = $data;
    }
}
