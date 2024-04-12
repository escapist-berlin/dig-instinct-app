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
        Schema::create('release_style', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('release_id');
            $table->foreign('release_id')->references('id')->on('releases')->onDelete('cascade');
            $table->unsignedBigInteger('style_id');
            $table->foreign('style_id')->references('id')->on('styles')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['release_id', 'style_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('release_style');
    }
};
