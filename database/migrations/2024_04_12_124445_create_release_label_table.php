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
        Schema::create('release_label', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('release_id');
            $table->foreign('release_id')->references('id')->on('releases')->onDelete('cascade');
            $table->unsignedBigInteger('label_id');
            $table->foreign('label_id')->references('id')->on('labels')->onDelete('cascade');
            $table->string('catno')->nullable();
            $table->timestamps();

            $table->unique(['release_id', 'label_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('release_label');
    }
};
