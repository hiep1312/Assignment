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
        Schema::create('order_shippings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('recipient_name', 255);
            $table->string('phone', 20);
            $table->string('province', 100);
            $table->string('district', 100);
            $table->string('ward', 100);
            $table->string('street', 255)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_shippings');
    }
};
