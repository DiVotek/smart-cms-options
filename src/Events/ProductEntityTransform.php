<?php

namespace SmartCms\Options\Events;

use SmartCms\Options\Dto\OptionDto;
use SmartCms\Options\Dto\OptionValueDto;
use SmartCms\Options\Models\Option;
use SmartCms\Options\Models\OptionValue;
use SmartCms\Store\Models\Product;
use SmartCms\Store\Repositories\Product\ProductEntityDto as ProductProductEntityDto;

class ProductEntityTransform
{
   public function __invoke(ProductProductEntityDto $dto)
   {
      $values = Product::query()->where('id', $dto->id)->first()->optionValues;
      $options = Option::query()->whereIn('id', $values->pluck('option_id'))->get();
      $options->transform(function (Option $option) use ($values) {
         return (new OptionDto($option->id, $option->name, $option->required, $values->where('option_id', $option->id)->map(
            function (OptionValue $value) use ($option) {
               $selected = $option->default_value == $value->id;
               return (new OptionValueDto($value->id, $value->name(), $value->image, $value->pivot->price, $value->pivot->sign, $selected))->get();
            }
         )->toArray()))->get();
      });
      $dto->setExtraValue('options', $options->toArray());
   }
}
