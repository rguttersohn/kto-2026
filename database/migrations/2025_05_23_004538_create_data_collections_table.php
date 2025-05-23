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
        Schema::connection('supabase')->create('collections.data', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->geometry('geometry', srid: 4326 )->nullable();
            $table->jsonb('data');
            $table->foreignId('collection_id')->constrained('collections.collections', 'id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('supabase')->dropIfExists('collections.data');
    }
};
