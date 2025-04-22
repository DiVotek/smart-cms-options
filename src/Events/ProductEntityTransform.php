<?php

namespace SmartCms\Options\Events;

use SmartCms\Options\Models\Option;
use SmartCms\Options\Resources\OptionResource;

class ProductEntityTransform
{
    public function __invoke(&$dto, $product)
    {
        $dto['options'] =  [];
        $values = $product->optionValues;
        $options = Option::query()->whereIn('id', $values->pluck('option_id'))->get();
        $options->transform(function ($option) use ($values) {
            return OptionResource::make($option, ['values' => $values->where('option_id', $option->id)])->get();
        });
        $dto['options'] = $options->toArray();
        // $options = $product->options->map(function ($el) use ($values) {
        //     return OptionResource::make($el, ['values' => $values->where('option_id', $el->id)])->get();
        //     // ->map(function ($value) use ($el) {
        //     //     return [
        //     //         'id' => $value->id,
        //     //         'name' => $value->name(),
        //     //         'image' => $value->image,
        //     //         'price' => $value?->pivot?->price ?? 0,
        //     //         'sign' => $value?->pivot?->sign ?? '',
        //     //         'selected' => $value->id == $el->default_value,
        //     //     ];
        //     // })->toArray(),
        //     // ])->get();
        //     //     'values' => $values->where('option_id', $el->id)->map(function ($value) use ($el) {
        //     //         return [
        //     //             'id' => $value->id,
        //     //             'name' => $value->name(),
        //     //             'image' => $value->image,
        //     //             'price' => $value?->pivot?->price ?? 0,
        //     //             'sign' => $value?->pivot?->sign ?? '',
        //     //             'selected' => $value->id == $el->default_value,
        //     //         ];
        //     //     })->toArray(),
        //     // ];
        // })->toArray();
        // dd($options);
        // dd($values, $product->optionValues, $product->options);
        // $options = $product->options->map(function ($el) use ($product) {
        //     return OptionResource::make($el, ['product_id' => $product->id])->get();
        // });
        // dd($options);
        // // $values = Product::query()->where('id', $dto->id)->first()->optionValues;
        // // $options = Option::query()->whereIn('id', $values->pluck('option_id'))->get();
        // // $options->transform(function (Option $option) use ($values) {
        // //     return (new OptionDto($option->id, $option->name, $option->required, $values->where('option_id', $option->id)->map(
        // //         function (OptionValue $value) use ($option) {
        // //             $selected = $option->default_value == $value->id;
        // //             return (new OptionValueDto($value->id, $value->name(), $value->image, $value->pivot->price, $value->pivot->sign, $selected))->get();
        // //         }
        // //     )->toArray()))->get();
        // // });
        // $dto->setExtraValue('options', $options->toArray());
    }
}
