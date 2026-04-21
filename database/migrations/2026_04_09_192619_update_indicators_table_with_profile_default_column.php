<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('indicators.indicators', function (Blueprint $table) {
            $table->boolean('profile_default')->default(false);
            $table->string('visualization_type')->nullable()->after('profile_default');

        });
    }

    public function down(): void
    {
        Schema::table('indicators.indicators', function (Blueprint $table) {
            $table->dropColumn('profile_default');
            $table->dropColumn('visualization_type');
        });
    }
};
