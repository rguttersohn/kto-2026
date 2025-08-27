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
        Schema::connection('supabase')->create('well_being_index.domain_indicator', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('indicator_id')->constrained('indicators.indicators', 'id');
            $table->foreignId('domain_id')->constrained('domains.domains', 'id')->cascadeOnDelete();
            $table->foreignId('indicator_data_format_id')->constrained('indicators.data_formats', 'id')->cascadeOnDelete();
            $table->foreignId('indicator_breakdown_id')->constrained('indicators.breakdowns', 'id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('supabase')->dropIfExists('well_being_index.domain_indicator');
    }
};
