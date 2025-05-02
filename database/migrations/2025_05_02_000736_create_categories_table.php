<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('supabase')->create('indicators.categories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->tinyText('name');
            $table->string('slug')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('indicators.categories', 'id')->nullOnDelete();
        });

        $categories = [
            [
                'name' => 'Demographics',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'subcategories' => [],
            ],
            [
                'name' => 'Economic Conditions',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'subcategories' => [
                    [
                        'name' => 'Poverty',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Income',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Labor Force Statistics',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Income Support',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Financial Security',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Economic Resources',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ],
            ],
            [
                'name' => 'Housing and Homelessness',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'subcategories' => [
                    [
                        'name' => 'Housing Availability and Affordability',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Housing Conditions',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Homelessness',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
            ],
            [
                'name' => 'Health and Mental Health',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'subcategories' => [
                    [
                        'name' => 'Coronavirus (COVID-19)',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'General Health',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Infant and Maternal Health',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Asthma',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Insurance',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Nutrition and Food Security',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Mental Health and Other Services',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Early Intervention',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Environment',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Health Resources',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],

                ],
            ],
            [
                'name' => 'Early Care and Education',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'subcategories' => [
                    [
                        'name' => 'Population of Children Under 5',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Enrollment',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Utilization',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Affordability',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Quality',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
            ],
            [
                'name' => 'Education',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'subcategories' => [
                    [
                        'name' => 'Student Characteristics',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'School Characteristics',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Student Performance Metrics',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Graduation Outcomes',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ],
            ],
            [
                'name' => 'Youth and Juvenile Justice',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'subcategories' => [
                    [
                        'name' => 'Teen Births',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Teen Employment and Idleness',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Juvenile Justice',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
            ],
            [
                'name' => 'Child Welfare and Community Safety',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'subcategories' =>[
                    [
                        'name' => 'Abuse and Neglect',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Prevention',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Foster Care',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Domestic Violence',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Community Safety',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],

                ]
            ]
            
        ];
        

        foreach ($categories as $category) {
            
            $category_record = Category::create($category);

            $subcategories = $category['subcategories'];
            
            foreach ($subcategories as $subcategory) {
                
                $subcategory['parent_id'] = $category_record->id;

                Category::create($subcategory);
            }
        }

       
      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('supabase')->dropIfExists('indicators.categories');
    }
};
