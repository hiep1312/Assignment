<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variant_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->integer('stock')->default(0);
            $table->integer('reserved')->default(0);
            $table->integer('sold_number')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_inventory');
    }
};
