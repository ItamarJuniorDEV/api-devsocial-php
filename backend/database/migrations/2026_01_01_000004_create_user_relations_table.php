<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_from')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_to')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_from', 'user_to']);
            $table->index(['user_from']);
            $table->index(['user_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_relations');
    }
};
