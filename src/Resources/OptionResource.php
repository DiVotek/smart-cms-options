<?php

namespace SmartCms\Options\Resources;

use SmartCms\Core\Resources\BaseResource;

class OptionResource extends BaseResource
{
    protected function prepareData($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'required' => $this->required,
            'values' => $this->getValues(),
        ];
    }

    public function getValues(): array
    {
        $values = $this->context('values', []);
        return $values->map(function ($el) {
            $el->isSelected = $el->id == $this->resource->default_value;
            return OptionValueResource::make($el)->get();
        })->toArray();
    }
}
