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
        Schema::table('scraped_articles', function (Blueprint $table) {
            $table->foreignId('suggested_author_id')->nullable()->after('source_id')->constrained('authors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scraped_articles', function (Blueprint $table) {
            $table->dropForeign(['suggested_author_id']);
            $table->dropColumn('suggested_author_id');
        });
    }
};
