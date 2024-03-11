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
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->string('code')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('job_count')->nullable();
            $table->unsignedBigInteger('discount')->nullable();
            $table->string('status')->nullable();
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade') ;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
