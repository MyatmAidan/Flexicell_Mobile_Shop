<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('blog_images')) {
            return;
        }
        Schema::create('blog_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_content_id')->constrained('blog_contents')->cascadeOnDelete();
            $table->string('image_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_images');
    }
};
