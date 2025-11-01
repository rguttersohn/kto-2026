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
        Schema::create('assets.asset_schema', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->jsonb('schema');
            $table->foreignId('asset_category_id')->constrained('assets.asset_categories', 'id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets.asset_schema');
    }
};
