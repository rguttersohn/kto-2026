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
        
        Schema::connection('supabase')->create('well_being_index.scores', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('domain_id')->constrained('domains.domains', 'id')->cascadeOnDelete();
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

        Schema::dropIfExists('well_being_index.scores');

    }
};
