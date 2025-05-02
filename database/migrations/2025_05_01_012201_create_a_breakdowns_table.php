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
        Schema::connection('supabase')->create('public.breakdown_types', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('name');
            $table->string('slug')->unique();
            $table->tinyText('description')->nullable();
        });
        
        Schema::connection('supabase')->create('public.breakdowns', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('name');
            $table->foreignId('breakdown_type_id')->constrained('public.breakdown_types','id')->cascadeOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('supabase')->dropIfExists('public.breakdowns');

        Schema::connection('supabase')->dropIfExists('public.breakdown_types');

        
    }
};
