<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $roles = [
            ['name' => 'user'],
            ['name' => 'editor'],
            ['name' => 'admin'],
        ];

        Role::insert($roles);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
