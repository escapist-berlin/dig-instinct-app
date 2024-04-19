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
        Schema::table('releases', function (Blueprint $table) {
            $table->string('image_full_uri')->nullable()->after('kollektivx_is_restored');
            $table->string('image_thumbnail_uri')->nullable()->after('image_full_uri');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('releases', function (Blueprint $table) {
            $table->dropColumn('image_full_uri');
            $table->dropColumn('image_thumbnail_uri');
        });
    }
};
