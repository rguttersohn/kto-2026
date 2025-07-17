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
        Schema::connection('supabase')->create('well_being_index.category_indicator', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('domain_id')->constrained('domains.domains', 'id');
            $table->foreignId('category_id')->constrained('indicators.categories', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('supabase')->dropIfExists('well_being_index.category_indicator');
    }
};
