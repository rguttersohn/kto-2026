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
        Schema::connection('supabase')->create('indicators.data', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->decimal('data', 10, 2);
            $table->foreignId('data_format_id')->constrained('indicators.data_formats', 'id');
            $table->integer('timeframe');
            $table->foreignId('location_id')->constrained('locations.locations', 'id')->cascadeOnDelete();
            $table->foreignId('breakdown_id')->nullable()->constrained('indicators.breakdowns', 'id')->cascadeOnDelete();
            $table->foreignId('indicator_id')->constrained('indicators.indicators', 'id')->cascadeOnDelete();
            $table->index('indicator_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('supabase')->dropIfExists('indicators.data');
    }
};
