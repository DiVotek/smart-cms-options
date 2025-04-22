<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Options\Models\Option;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Option::getDb(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('status')->default(true)->index();
            $table->integer('sorting')->default(0)->index();
            $table->boolean('required')->default(false);
            $table->unsignedBigInteger('default_value')->nullable();
            $table->timestamps();

            $table->index(['status', 'sorting']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(Option::getDb());
    }
};
