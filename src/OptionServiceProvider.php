<?php

namespace SmartCms\Options;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use SmartCms\Options\Admin\Actions\Navigation\ProductPages;
use SmartCms\Options\Admin\Actions\Navigation\ProductSubNavigation;
use SmartCms\Options\Admin\Actions\Navigation\Resources;
use SmartCms\Options\Events\CartItemTransform;
use SmartCms\Options\Events\CartProductCalculate;
use SmartCms\Options\Events\ProductEntityTransform;
use SmartCms\Options\Events\ProductPrice;
use SmartCms\Options\Models\Option;
use SmartCms\Options\Models\OptionValue;
use SmartCms\Store\Models\Product;

class OptionServiceProvider  extends ServiceProvider
{
   public function register()
   {
      $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
      Event::listen('cms.admin.navigation.resources', Resources::class);
      Event::listen('cms.admin.product.pages', ProductPages::class);
      Event::listen('cms.admin.product.sub_navigation', ProductSubNavigation::class);
      Event::listen('cms.product-entity.transform', ProductEntityTransform::class);
      Event::listen('cms.product.calculate-price', ProductPrice::class);
      Event::listen('cms.cart-item.transform', CartItemTransform::class);
      Event::listen('cms.product.cart.calculate', CartProductCalculate::class);
   }

   public function boot()
   {
      Product::resolveRelationUsing('options', function ($product) {
         return $product->hasManyThrough(Option::class, OptionValue::class, 'id', 'id', 'id', 'option_id')
            ->distinct();
      });
      Product::resolveRelationUsing('optionValues', function ($product) {
         return $product->belongsToMany(OptionValue::class, Product::getOptionsDb())
            ->withPivot('sign', 'price', 'origin_price');
      });
   }
}
