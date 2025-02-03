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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('thumbnail_id')->nullable()->constrained('files')->onDelete('set null');
            $table->foreignId('pdf_id')->nullable()->constrained('files')->onDelete('set null');
            $table->string('title')->index();
            $table->integer('volume')->nullable();
            $table->string('edition')->nullable();
            $table->integer('pages');
            $table->string('isbn')->unique();
            $table->string('author');
            $table->string('genre');
            $table->string('publisher');
            $table->text('description')->nullable();
            $table->year('year')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
