<?php

namespace SmartCms\Options\Admin\Actions\Navigation;

use SmartCms\Options\Admin\Resources\Product\EditOptions;

class ProductSubNavigation
{
   public function __invoke(array &$pages)
   {
      $pages[] = EditOptions::class;
   }
}
