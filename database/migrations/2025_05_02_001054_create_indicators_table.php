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
        Schema::connection('supabase')->create('indicators.indicators', function (Blueprint $table) {
            $table->id();
            $table->tinyText('name');
            $table->string('slug')->unique();
            $table->foreignId('category_id')->constrained('indicators.categories', 'id')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->text('source')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('supabase')->dropIfExists('indicators.indicators');
    }
};
