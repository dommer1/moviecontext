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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scraped_articles');
    }
};
