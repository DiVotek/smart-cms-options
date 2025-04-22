<?php

namespace SmartCms\Options\Events\Navigation;

use SmartCms\Options\Admin\Resources\OptionResource;

class Resources
{
    public function __invoke(array &$items)
    {
        $items =  array_merge([
            OptionResource::class,
        ], $items);
    }
}
