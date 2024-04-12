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
        Schema::create('release_list', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_list_id');
            $table->foreign('user_list_id')->references('id')->on('user_lists')->onDelete('cascade');
            $table->unsignedBigInteger('release_id');
            $table->foreign('release_id')->references('id')->on('releases')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_list_id', 'release_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('release_list');
    }
};
