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
            $table->boolean('status')->default(true);
            $table->integer('sorting')->default(0);
            $table->boolean('required')->default(false);
            $table->unsignedBigInteger('default_value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(Option::getDb());
    }
};
