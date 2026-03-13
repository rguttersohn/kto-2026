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
        Schema::create('indicators.meta', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('indicator_id')->nullable()->constrained('indicators.indicators', 'id')->cascadeOnDelete();
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->text('og_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicators.meta');
    }
};
