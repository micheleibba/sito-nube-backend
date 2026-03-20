<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_it');
            $table->string('slug_en')->unique();
            $table->string('slug_it')->unique();
            $table->text('text_en');
            $table->text('text_it');
            $table->string('cover_image')->nullable();
            $table->string('meta_description_en', 500)->nullable();
            $table->string('meta_description_it', 500)->nullable();
            $table->boolean('published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
