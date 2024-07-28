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
        Schema::table('release_list', function (Blueprint $table) {
            $table->timestamp('date_added_to_wantlist')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('release_list', function (Blueprint $table) {
            $table->dropColumn('date_added_to_wantlist');
        });
    }
};
