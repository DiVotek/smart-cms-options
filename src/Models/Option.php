<?php

namespace SmartCms\Options\Models;

use SmartCms\Core\Models\BaseModel;
use SmartCms\Core\Traits\HasSorting;
use SmartCms\Core\Traits\HasStatus;
use SmartCms\Core\Traits\HasTranslate;

class Option extends BaseModel
{
    use HasSorting;
    use HasStatus;
    use HasTranslate;

    protected $fillable = [
        'name',
        'status',
        'sorting',
        'required',
        'default_value',
    ];

    public function values()
    {
        return $this->hasMany(OptionValue::class);
    }

    public function defaultValue()
    {
        return $this->hasOne(OptionValue::class, 'id', 'default_value');
    }
}
