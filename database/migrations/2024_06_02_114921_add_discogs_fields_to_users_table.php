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
        Schema::table('users', function (Blueprint $table) {
            $table->string('discogs_username')->unique()->nullable()->default(null);
            $table->string('discogs_oauth_token')->nullable()->default(null);
            $table->string('discogs_oauth_token_secret')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('discogs_username');
            $table->dropColumn('discogs_oauth_token');
            $table->dropColumn('discogs_oauth_token_secret');
        });
    }
};
