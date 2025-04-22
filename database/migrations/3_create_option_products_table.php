<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use SmartCms\Options\Models\OptionValue;
use SmartCms\Store\Models\Product;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(sconfig('database_table_prefix', 'smart_cms_') . 'product_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('option_value_id')->constrained(OptionValue::getDb())->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('product_id')->constrained(Product::getDb())->onUpdate('cascade')->onDelete('cascade');
            $table->string('sign')->default('+');
            $table->double('price');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(sconfig('database_table_prefix', 'smart_cms_') . 'product_options');
    }
};
