<?php

namespace SmartCms\Options\Admin\Actions\Navigation;

use SmartCms\Options\Admin\Resources\Product\EditOptions;

class ProductPages
{
   public function __invoke(array &$pages)
   {
      $pages = array_merge([
         'options' => EditOptions::route('/{record}/options'),
      ], $pages);
   }
}
