<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_suggestions', function (Blueprint $table) {
            $table->id();
            $table->string('source_url');
            $table->string('source_name');
            $table->string('original_title');
            $table->string('title_en');
            $table->string('title_it');
            $table->text('text_en');
            $table->text('text_it');
            $table->string('meta_description_en', 500)->nullable();
            $table->string('meta_description_it', 500)->nullable();
            $table->text('cover_image_url')->nullable(); // DALL-E generated
            $table->string('cover_image_path')->nullable(); // saved locally
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_suggestions');
    }
};
