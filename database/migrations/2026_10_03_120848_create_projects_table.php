<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->json('title')->nullable();
            $table->string('slug')->nullable();
            $table->json('subtitle')->nullable();
            $table->string('owner')->nullable();
            $table->json('short_description')->nullable();
            $table->json('long_description')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('github_link')->nullable();
            $table->string('project_link')->nullable();
            $table->date('start_date')->nullable();
            $table->date('project_date')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
