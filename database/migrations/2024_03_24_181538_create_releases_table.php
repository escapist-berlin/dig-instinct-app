<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('releases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('discogs_id')->nullable();
            $table->unsignedBigInteger('discogs_master_id')->nullable();
            $table->unsignedBigInteger('kollektivx_id')->nullable();
            $table->string('title');
            $table->string('formats')->nullable();
            $table->string('country')->nullable();
            $table->string('released')->nullable();
            $table->integer('year')->nullable();
            $table->float('rating_average')->nullable();
            $table->integer('rating_count')->nullable();
            $table->integer('have')->nullable();
            $table->integer('want')->nullable();
            $table->integer('num_for_sale')->nullable();
            $table->float('lowest_price')->nullable();
            $table->text('uri')->nullable();
            $table->string('kollektivx_uri')->nullable();
            $table->boolean('kollektivx_is_raw')->default(false);
            $table->boolean('kollektivx_is_restored')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};
