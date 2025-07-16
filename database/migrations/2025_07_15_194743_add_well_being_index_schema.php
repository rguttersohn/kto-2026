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
        DB::connection('supabase')->statement('CREATE SCHEMA IF NOT EXISTS well_being_index');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('supabase')->statement('DROP SCHEMA IF EXISTS well_being_index CASCADE');
    }
};
