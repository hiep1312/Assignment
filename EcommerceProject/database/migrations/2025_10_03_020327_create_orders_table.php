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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('order_code', 100)->unique();
            $table->bigInteger('total_amount');
            $table->bigInteger('shipping_fee')->default(0);
            $table->tinyInteger('status')->default(1)->index()
                ->comment('Status rules: 1 -> new | 2 -> confirmed | 3 -> processing | 4 -> shipped | 5 -> delivered | 6 -> completed | 7 -> failed | 8 -> buyer_cancel | 9 -> admin_cancel');
            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->string('cancel_reason', 255)->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('processing_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
