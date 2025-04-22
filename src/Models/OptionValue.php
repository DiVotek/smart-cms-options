<?php

namespace SmartCms\Options\Models;

use SmartCms\Core\Models\BaseModel;
use SmartCms\Core\Traits\HasSorting;
use SmartCms\Core\Traits\HasStatus;
use SmartCms\Core\Traits\HasTranslate;
use SmartCms\Store\Models\Product;

class OptionValue extends BaseModel
{
    use HasSorting;
    use HasStatus;
    use HasTranslate;

    protected $guarded = [];

    public function option()
    {
        return $this->belongsTo(Option::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, Product::getOptionsDb(), 'option_value_id', 'product_id')
            ->withPivot('sign', 'price');
    }
}
