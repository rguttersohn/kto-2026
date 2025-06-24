<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\DataFormat;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('supabase')->create('indicators.data_formats', function (Blueprint $table) {
            $table->id();
            $table->tinyText('name');
            $table->timestamps();
        });

        $formats = [
            'Percent',
            'Number',
            'Dollar'
        ];

        foreach($formats as $format){

            DataFormat::create([
                'name' => $format
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('supabase')->dropIfExists('indicators.data_formats');
    }
};
