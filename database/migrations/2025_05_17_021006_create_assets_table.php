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
        Schema::connection('supabase')->create('assets.assets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('description');
            $table->geometry('geometry','point',4326);
            $table->foreignId('asset_category_id')->constrained('assets.asset_categories', 'id')->cascadeOnDelete();

        });

        DB::connection('supabase')->statement('CREATE INDEX asset_geometry_idx ON assets.assets USING GIST (geometry)');


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('supabase')->dropIfExists('assets.assets');
    }
};
