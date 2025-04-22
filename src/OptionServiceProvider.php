<?php

namespace SmartCms\Options;

use Illuminate\Support\ServiceProvider;
use SmartCms\Core\SmartCmsPanelManager;
use SmartCms\Options\Events\CartItemTransform;
use SmartCms\Options\Events\Navigation\ProductPages;
use SmartCms\Options\Events\Navigation\ProductSubNavigation;
use SmartCms\Options\Events\Navigation\Resources;
use SmartCms\Options\Events\ProductEntityTransform;
use SmartCms\Options\Events\ProductPrice;
use SmartCms\Options\Models\Option;
use SmartCms\Options\Models\OptionValue;
use SmartCms\Store\Admin\Resources\ProductResource;
use SmartCms\Store\Models\Product;
use SmartCms\Store\Resources\Cart\CartItemResource;
use SmartCms\Store\Resources\Product\ProductEntityResource;
use SmartCms\Store\Resources\Product\ProductResource as ProductProductResource;
use SmartCms\Store\Routes\ProductPriceHandler;

class OptionServiceProvider  extends ServiceProvider
{
    public function register()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'options');
        SmartCmsPanelManager::registerHook('navigation.resources', Resources::class);
        ProductResource::registerHook('pages', ProductPages::class);
        ProductResource::registerHook('sub_navigation', ProductSubNavigation::class);
    }

    public function boot()
    {
        Product::resolveRelationUsing('options', function ($product) {
            return $product->hasManyThrough(Option::class, OptionValue::class, 'id', 'id', 'id', 'option_id')
                ->distinct();
        });
        Product::resolveRelationUsing('optionValues', function ($product) {
            return $product->belongsToMany(OptionValue::class, Product::getOptionsDb())
                ->withPivot('sign', 'price');
        });
        ProductPriceHandler::registerHook('calculate-price', ProductPrice::class);
        CartItemResource::registerHook('transform.data', CartItemTransform::class);
        ProductEntityResource::registerHook('transform.data', ProductEntityTransform::class);
    }
}
