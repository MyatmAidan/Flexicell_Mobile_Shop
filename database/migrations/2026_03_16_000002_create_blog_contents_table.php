<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('blog_contents')) {
            return;
        }
        Schema::create('blog_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained('blogs')->cascadeOnDelete();
            $table->string('heading');
            $table->longText('content');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_contents');
    }
};
