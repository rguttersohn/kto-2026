<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\AssetCategory;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('supabase')->create('assets.asset_categories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('name');
            $table->foreignId('parent_id')->nullable()->constrained('assets.asset_categories', 'id')->nullOnDelete();
            $table->boolean('is_published')->default(false);

        });


        $categories = [
            'Bank',
            'Public Transportation' => [
                'Subway Stop',
                'Bus Stop',
            ],
            'Financial Empowerment Center',
            'Workforce Development',
            'NYCHA',
            'Housing Support Services' => [
                'DYCD Housing Assistance',
                'Free Legal Services',
                'Homebase'
            ],
            'Supportive Housing (SRO)',
            'Homeless Shelter',
            'Drop-in Center',
            'Emergency Food Assistance' => [
                'SNAP Center',
                'SNAP Enrollment Assistance',
                'WIC Program Site',
                'Food Pantry',
                'Soup Kitchen'
            ],
            'Food Retail' => [
                'Traditional Food Retail',
                'Farmers\' Market'
            ],
            'Mental Health Services',
            'Medical Facility' => [
                'Hospital',
                'Diagnostic and Treatment Center',
                'Clinic',
                'Mobile Clinic'
            ],
            'Open and Recreational Space'  =>[
                'Park',
                'Playground',
                'Garden',
                'Recreation and Athletics'
            ],
            'Contracted Childcare (Early Learn)' => [
                'Child Care Center',
                'Family Child Care'
            ],
            'Non Contracted (Voucher)' => [
                'Center',
                'Family',
                'Informal'
            ],
            'Pre-K Site' => [
                'Pre-K DOE School',
                'Pre-K DOE Pre-K Center',
                'Pre-K CBO Center'
            ],
            '3-K Site' => [
                '3-K DOE School',
                '3-K DOE Pre-K Center',
                '3-K CBO Center',
                '3-K Family'
            ],
            'Continuing Education',
            'Public School'=>[
                'Traditional Public School',
                'Charter',
                'Community School'
            ],
            'Afterschool Programs' => [
                'COMPASS Elementary',
                'COMPASS Middle School (SONYC)',
                'COMPASS High School',
                'COMPASS Explore',
                'Beacon',
                'Cornerstone',
                'Other After School Programs'
            ],
            'Public Safety' =>[
                'Police Station',
                'Fire Station',
                'EMS',
                'Detention Center'
            ],
            'ACS Preventive Services' => [
                'Family Support',
                'Therapeutic and Treatment Program'
            ],
            'Cultural Institution' => [
                'Public Library',
                'Museum',
                'Other Cultural Institutions'
            ],
            'Public Digital Resources' => [
                'Public Computer Centers',
                'Wi-Fi in Public and Open Spaces',
                'LinkNYC'
            ]
            

            
        ];

        foreach($categories as $parent=>$children){

            if(is_int($parent)){
                
                $parent_category = AssetCategory::create([
                    'name' => $children,
                ]);

                continue;
            }

            $parent_category = AssetCategory::create([
                'name' => $parent,
            ]);


            foreach($children as $child){
                
                AssetCategory::create([
                    'parent_id' => $parent_category->id,
                    'name' => $child
                ]);
                
            }
            

            
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('supabase')->dropIfExists('assets.asset_categories');
    }
};
