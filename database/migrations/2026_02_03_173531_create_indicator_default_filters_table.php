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
        Schema::create('indicators.default_filters', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignID('indicator_id')->constrained('indicators.indicators', 'id')->cascadeOnDelete();
            $table->integer('timeframe')->nullable();
            $table->foreignID('data_format_id')->nullable()->constrained('indicators.data_formats', 'id')->cascadeOnDelete();
            $table->foreignID('breakdown_id')->nullable()->constrained('indicators.breakdowns', 'id')->cascadeOnDelete();
            $table->foreignID('location_type_id')->nullable()->constrained('locations.location_types', 'id')->cascadeOnDelete();
            $table->foreignID('location_id')->nullable()->constrained('locations.locations', 'id')->cascadeOnDelete();
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
