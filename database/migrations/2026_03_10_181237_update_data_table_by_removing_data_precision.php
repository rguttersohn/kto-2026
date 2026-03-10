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
        Schema::connection('supabase')->table('indicators.data', function () {

            DB::connection('supabase')->statement('ALTER TABLE "indicators"."data" ALTER COLUMN "data" TYPE numeric');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('supabase')->statement('ALTER TABLE "indicators"."data" ALTER COLUMN "data" TYPE numeric(10, 2)');

    }
};
