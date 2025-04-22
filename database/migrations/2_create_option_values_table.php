<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Options\Models\Option;
use SmartCms\Options\Models\OptionValue;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(OptionValue::getDb(), function (Blueprint $table) {
            $table->id();
            $table->foreignId('option_id')->constrained(Option::getDb())->onDelete('cascade');
            $table->string('name');
            $table->boolean('status')->default(true)->index();
            $table->integer('sorting')->default(0)->index();
            $table->string('image')->nullable();
            $table->timestamps();

            $table->index(['status', 'sorting']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(OptionValue::getDb());
    }
};
