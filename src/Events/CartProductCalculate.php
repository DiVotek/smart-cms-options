<?php

namespace SmartCms\Options\Events;

use Illuminate\Support\Facades\DB;
use SmartCms\Store\Models\Product;

class CartProductCalculate
{
   public function __invoke(Product $product, array $data = [], float &$modifier = 0)
   {
      dd($product, $data, $modifier);
      $options = $data['options'] ?? [];
      foreach ($options as $option) {
         $optionValue = DB::table(sconfig('database_table_prefix', 'smart_cms_') . 'product_options')
            ->where('product_id', $product->id)
            ->where('option_value_id', $option)
            ->first();
         if (!$optionValue) {
            continue;
         }
         if ($optionValue->sign === '+') {
            $product->price += $optionValue->price;
         } else {
            $product->price -= $optionValue->price;
         }
      }
      dd($product->price);
      dd($product, $data);
   }
}
