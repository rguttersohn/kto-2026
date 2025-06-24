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
        Schema::connection('supabase')->create('collections.collections', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->tinyText('name');
            $table->text('description')->nullabe();
            $table->boolean('is_published')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('supabase')->dropIfExists('collections.collections');
    }
};
