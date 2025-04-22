<?php

namespace SmartCms\Options\Resources;

use SmartCms\Core\Resources\BaseResource;
use SmartCms\Store\Services\Calculator;

class OptionValueResource extends BaseResource
{
    protected function prepareData($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name(),
            'image' => $this->validateImage($this->resource->image ?? no_image()),
            'price' => Calculator::format($this->resource?->pivot?->price ?? 0),
            'original_price' => $this->resource?->pivot?->price ?? 0,
            'sign' => $this->resource?->pivot?->sign ?? '',
            'selected' => $this->resource?->isSelected ?? false,
        ];
    }
}
