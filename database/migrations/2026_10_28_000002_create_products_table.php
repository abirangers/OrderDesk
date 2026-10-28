<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 32)->unique();
            $table->string('name', 120);
            $table->decimal('price', 12, 2);
            // Signed on purpose: a negative stock value is visible, whereas an
            // UNSIGNED column would turn an oversell into a database error and
            // hide the bug behind a 500. The guard belongs in the application.
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};