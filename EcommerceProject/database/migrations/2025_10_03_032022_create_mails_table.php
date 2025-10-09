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
        Schema::create('mails', function (Blueprint $table) {
            $table->id();
            $table->string('subject', 255)->nullable();
            $table->text('body');
            $table->json('variable')->nullable();
            $table->tinyInteger('type')->default(0)
                ->comment('Type rules: 0 -> custom | 1 -> order_success | 2 -> order_failed | 3 -> shipping_update | 4 -> forgot_password | 5 -> register_success');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mails');
    }
};
