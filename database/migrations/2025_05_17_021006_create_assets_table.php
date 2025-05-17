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
        Schema::connection('supabase')->create('assets.assets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('description');
            $table->geometry('location', srid: 4326);
            $table->foreignId('category_id')->constrained('assets.asset_categories', 'id')->cascadeOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('supabase')->dropIfExists('assets.assets');
    }
};
