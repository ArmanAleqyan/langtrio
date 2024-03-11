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
        Schema::create('words', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('levels_id')->nullable();
            $table->unsignedBigInteger('text_id')->nullable();
            $table->string('word_ru')->nullable();
            $table->string('word_en')->nullable();
            $table->string('word_fr')->nullable();
            $table->string('audio_ru')->nullable();
            $table->string('audio_en')->nullable();
            $table->string('audio_fr')->nullable();
            $table->string('photo')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade') ;
            $table->foreign('levels_id')->references('id')->on('levels')->onDelete('cascade') ;
            $table->foreign('text_id')->references('id')->on('texts')->onDelete('cascade') ;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('words');
    }
};
