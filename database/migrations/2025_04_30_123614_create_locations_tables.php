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

        Schema::connection('supabase')->create('locations.location_types', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('name');
            $table->text('plural_name');
            $table->text('classification');
            $table->string('scope')->default('local');
            $table->boolean('is_rankable')->default(false);
            $table->boolean('has_community_profile')->default(false);
            
        });

        Schema::connection('supabase')->create('locations.locations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('fips', 15)->unique()->nullable();
            $table->string('district_id')->unique()->nullable();
            $table->string('legacy_district_id')->unique()->nullable();
            $table->text('name');
            $table->foreignId('location_type_id')->constrained('location_types', 'id')->cascadeOnDelete();
            $table->boolean('is_uninhabited')->default(false);
            $table->date('valid_starting_on');
            $table->date('valid_ending_on')->nullable();

        });

        Schema::connection('supabase')->create('locations.geometries', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('location_id')->constrained('locations', 'id')->cascadeOnDelete();
            $table->text('type')->required();
            $table->geometry('geometry',  srid: 4326 );
            $table->date('valid_starting_on');
            $table->date('valid_ending_on')->nullable();
          
        });


        DB::connection('supabase')->statement('CREATE INDEX location_geometry_idx ON locations.geometries USING GIST (geometry)');
     
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::connection('supabase')->dropIfExists('locations.geometries');

        Schema::connection('supabase')->dropIfExists('locations.locations');

        Schema::connection('supabase')->dropIfExists('locations.location_types');

    }
};
