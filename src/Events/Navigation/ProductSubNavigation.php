<?php

namespace SmartCms\Options\Events\Navigation;

use SmartCms\Options\Admin\Resources\Product\EditOptions;

class ProductSubNavigation
{
    public function __invoke(array &$pages)
    {
        $pages[] = EditOptions::class;
    }
}
