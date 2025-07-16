<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        
        Schema::connection('supabase')->create('well_being_index.rankings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('indicator_category_id')->constrained('indicators.categories', 'id')->cascadeOnDelete();
            $table->integer('year');
            $table->float('score');
            $table->foreignId('location_id')->constrained('locations.locations', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    
    {

        Schema::dropIfExists('well_being_index.rankings');

    }
};
