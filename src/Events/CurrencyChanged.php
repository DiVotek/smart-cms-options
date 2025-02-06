<?php

namespace SmartCms\Options\Events;

use Illuminate\Support\Facades\DB;
use SmartCms\Store\Models\Product;
use SmartCms\Store\Services\Calculator;

class CurrencyChanged
{
   public function __invoke()
   {
      $optionValues = DB::table(sconfig('database_table_prefix', 'smart_cms_') . 'product_options')->get();
      foreach ($optionValues as $optionValue) {
         $price = Calculator::calculate($optionValue->origin_price);
         DB::table(sconfig('database_table_prefix', 'smart_cms_') . 'product_options')->where('id', $optionValue->id)->where('product_id', $optionValue->product_id)->update([
            'price' => $price
         ]);
      }
   }
}
