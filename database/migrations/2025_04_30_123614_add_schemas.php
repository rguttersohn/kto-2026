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
        DB::connection('supabase')->statement('CREATE SCHEMA IF NOT EXISTS assets');
        DB::connection('supabase')->statement('CREATE SCHEMA IF NOT EXISTS locations');
        DB::connection('supabase')->statement('CREATE SCHEMA IF NOT EXISTS indicators');
        DB::connection('supabase')->statement('CREATE SCHEMA IF NOT EXISTS domains');
        DB::connection('supabase')->statement('CREATE SCHEMA IF NOT EXISTS collections');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('supabase')->statement('DROP SCHEMA IF EXISTS assets CASCADE');
        DB::connection('supabase')->statement('DROP SCHEMA IF EXISTS locations CASCADE');
        DB::connection('supabase')->statement('DROP SCHEMA IF EXISTS indicators CASCADE');
        DB::connection('supabase')->statement('DROP SCHEMA IF EXISTS domains CASCADE');
        DB::connection('supabase')->statement('DROP SCHEMA IF EXISTS collections CASCADE');

    }
};
