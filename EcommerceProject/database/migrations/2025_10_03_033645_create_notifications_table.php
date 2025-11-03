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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->default(0)->index()
                ->comment('Type rules: 0 -> custom | 1 -> order_update | 2 -> payment_update | 3 -> promotion | 4 -> account_update | 5 -> maintenance | 6 -> internal_system');
            $table->string('title', 255);
            $table->text('message');
            $table->json('variable')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
