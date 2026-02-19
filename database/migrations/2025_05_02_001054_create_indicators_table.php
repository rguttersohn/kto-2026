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
            $table->timestamps();
            $table->tinyText('name');
            $table->foreignId('category_id')->constrained('indicators.categories', 'id')->nullOnDelete();
            $table->text('definition')->nullable();
            $table->text('source')->nullable();
            $table->text('note')->nullable();
            $table->text('data_flag')->nullable();
            $table->boolean('is_published')->default('false');
            $table->boolean('is_archived')->default('false');
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
