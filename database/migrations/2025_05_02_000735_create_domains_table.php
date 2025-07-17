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
        Schema::connection('supabase')->create('domains.domains', function (Blueprint $table) {
            
            $table->id();
            $table->timestamps();
            $table->text('name');
            $table->text('definition');
            $table->boolean('is_rankable');
            
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('supabase')->dropIfExists('domains.domains');
    }
};
