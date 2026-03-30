<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_permissions')) {
            Schema::create('user_permissions', function (Blueprint $table) {
                $table->id();
                $table->ulid('user_id');
                $table->ulid('permission_id');
                $table->string('type'); // 'grant' or 'revoke'
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
                $table->unique(['user_id', 'permission_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};
