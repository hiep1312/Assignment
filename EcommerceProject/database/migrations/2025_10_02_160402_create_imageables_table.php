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
        Schema::create('imageables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('image_id')->constrained('images')->onDelete('cascade');
            $table->numericMorphs('imageable');
            $table->boolean('is_main')->default(false);
            $table->integer('position')->nullable();
            $table->timestamps();

            $table->index(['imageable_type', 'imageable_id', 'is_main']);
            $table->index(['imageable_type', 'imageable_id', 'position']);
            $table->comment('Polymorphic: Product | Banner | Blog');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imageables');
    }
};
