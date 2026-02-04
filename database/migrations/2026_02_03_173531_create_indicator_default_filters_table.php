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
        Schema::create('indicators.indicator_default_filters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicator_id')->constrained('indicators.indicators')->cascadeOnDelete();
            $table->string('filter_type');
            $table->integer('default_value_id');
            $table->timestamps();
            
            //makes composite index
            $table->unique(['indicator_id', 'filter_type']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicators.indicator_default_filters');
    }
};
