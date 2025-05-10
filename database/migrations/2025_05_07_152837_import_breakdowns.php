<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Breakdown;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $breakdowns = [
            'All',
            'Age Group'=>[
                'Children 18 Years and Under',
                'Adults 18 Years and Older',
                'Youth 18 to 24 Years',
                'Adults 25 Years and Older'
            ],
            'Race/Ethnicity' => [
                'Black',
                'White',
                'Hispanic or Latino',
                'Asian and Pacific Islander',
                'Combination or Another Race',
                'Native American'
            ],
            'Poverty Level' => [
                'Below 100% FPL',
                '100 to 199% FPL',
                'Below 200% FPL',
                '200 to 399% FPL',
                '400% FPL and above'
            ],
            'Household Type' => [
                'Family Households with Children',
                'Family Households without Children',
                'Non-family Households',
                'Married Couples',
                'Single Mothers',
                'Single Fathers',
                'Grandparents',
                'Other'
            ],
            'Internet Access' => [
                'Households without Internet',
                'Children without Internet',
                'Households without Internet by Income',
                'Internet Access by Type'
            ],
            'Population Types' => [
                'Households',
                'Individuals',
                'Total Population Living in Concentrated Poverty',
                'Children Living in Concentrated Poverty',
                'Poor Population Living in Concentrated Poverty',
                'Poor Children Living in Concentrated Poverty'
            ],
            'Educational Level' => [
                'Less than High School Degree',
                'High School Degree',
                'Some College',
                'Associate\'s Degree',
                'Bachelor\'s Degree or Higher'
            ],
            'Income Level' =>[
                'Under $15,000',
                '$25,000 to $34,999',
                '$35,000 to $49,999',
                '$50,000 to $74,999',
                '$75,000 to $99,999',
                '$100,000 to $199,999',
                '$200,000 or more'
            ],
            'Child and Dependent Care Credit' =>[
                'City CDCC',
                'State CDCC',
                'Average City CDCC',
                'Average State CDCC'
            ],
        ];

        foreach($breakdowns as $parent=>$children){

            if(is_int($parent)){

                $parent_breakdown = Breakdown::create([
                    'name' => $children,
                ]);

                continue;
            }

            $parent_breakdown = Breakdown::create([
                'name' => $parent,
            ]);


            foreach($children as $child){
                
                Breakdown::create([
                    'parent_id' => $parent_breakdown->id,
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
        //
    }
};
