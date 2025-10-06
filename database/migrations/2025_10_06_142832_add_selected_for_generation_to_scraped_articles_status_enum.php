<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to recreate the table with the new enum values
        // since SQLite doesn't support ALTER TABLE for CHECK constraints

        // Get all existing data
        $articles = DB::table('scraped_articles')->get();

        // Drop and recreate the table
        Schema::dropIfExists('scraped_articles');

        Schema::create('scraped_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content_summary');
            $table->string('author_name')->nullable();
            $table->timestamp('published_at');
            $table->string('image_url')->nullable();
            $table->string('original_url');
            $table->longText('html_snapshot');
            $table->enum('status', ['pending', 'selected_for_generation', 'processed', 'error'])->default('pending');
            $table->foreignId('source_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Restore the data
        foreach ($articles as $article) {
            DB::table('scraped_articles')->insert((array) $article);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get all existing data
        $articles = DB::table('scraped_articles')->get();

        // Drop and recreate the table with original enum
        Schema::dropIfExists('scraped_articles');

        Schema::create('scraped_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content_summary');
            $table->string('author_name')->nullable();
            $table->timestamp('published_at');
            $table->string('image_url')->nullable();
            $table->string('original_url');
            $table->longText('html_snapshot');
            $table->enum('status', ['pending', 'processed', 'error'])->default('pending');
            $table->foreignId('source_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Restore the data (filter out invalid status values)
        foreach ($articles as $article) {
            if ($article->status !== 'selected_for_generation') {
                DB::table('scraped_articles')->insert((array) $article);
            } else {
                // Set to pending if it was selected_for_generation
                $article->status = 'pending';
                DB::table('scraped_articles')->insert((array) $article);
            }
        }
    }
};
