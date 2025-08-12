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
        DB::connection('supabase')->statement('CREATE EXTENSION IF NOT EXISTS postgis SCHEMA extensions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
