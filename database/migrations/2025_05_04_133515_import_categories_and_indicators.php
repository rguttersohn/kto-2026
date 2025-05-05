<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Category;
use App\Models\Indicator;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $categories = [
            [
                'name' => 'Demographics',
                'subcategories' => [
                    [
                        'name' => 'Population',
    
    
                        'indicators' =>[
                            [
                                'name' => 'Total Population',
            
            
                                'definition' => 'Total population is the total number of people living in a given area.',
                            ],
                            [
                                'name' => 'Child Population',
            
            
                                'definition' => 'Child population is the total number of children living in a given area.',
                            ],
                            [
                                'name' => 'Households and Families',
            
            
                                'definition' => 'Households and families are the total number of households and families living in a given area.',
                            ],
                            [
                                'name' => 'Census Self-Response Rates',
            
            
                                'definition' => 'Census self-response rates are the percentage of households that responded to the census.',
                            ],
                            [
                                'name' => 'Hard-to-Count Households',
            
            
                                'definition' => 'Hard-to-count households are the percentage of households that are difficult to count in the census.',
                            ],
                            [
                                'name' => 'Citizenship',
            
            
                                'definition' => 'Citizenship is the percentage of people who are citizens of a given area.',
                            ],
                            [
                                'name' => 'Foreign Born Population',
            
            
                                'definition' => 'Foreign born population is the percentage of people who are foreign born in a given area.',
                            ],
                            [
                                'name' => 'Limited English Proficiency',
            
            
                                'definition' => 'Limited English proficiency is the percentage of people who have limited English proficiency in a given area.',
                            ],
                            [
                                'name' => 'Voter Registration',
            
            
                                'definition' => 'Voter registration is the percentage of people who are registered to vote in a given area.',
                            ],
                            [
                                'name' => 'Household Internet Access',
            
            
                                'definition' => 'Household internet access is the percentage of households that have internet access in a given area.',
                            ]],
                        ]
                ],
            ],
            [
                'name' => 'Economic Conditions',
                'subcategories' => [
                    [
                        'name' => 'Poverty',
    
    
                        'indicators' =>[
                            [
                                'name' => 'Poverty',
            
            
                                'definition' => 'Poverty is the percentage of people who are living in poverty in a given area.',
                            ],
                            [
                                'name' => 'Child Poverty',
            
            
                                'definition' => 'Child poverty is the percentage of children who are living in poverty in a given area.',
                            ],
                            [
                                'name' => 'Concentrated Poverty',
            
            
                                'definition' => 'Concentrated poverty is the percentage of people who are living in concentrated poverty in a given area.',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Income',
    
    
                        'indicators' => [
                            [
                                'name' => 'Household Income',
            
            
                                'definition' => 'Household income is the total income of all people living in a single housing unit.',
                            ],
                            [
                                'name' => 'Family Income',
            
            
                                'definition' => 'Family income is the total income of all people living in a single family unit.',
                            ],
                            [
                                'name' => 'Median Income',
            
            
                                'definition' => 'Median income is the middle value of all incomes in a given area.',
                            ],
                            [
                                'name' => 'Income Diversity Ratio',
            
            
                                'definition' => 'Income diversity ratio is the ratio of the number of different income sources to the total number of income sources.',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Labor Force Statistics',
    
    
                        'indicators' =>[
                            [
                                'name' => 'Uenmployment Rate', 
            
            
                                'definition' => 'Unemployment rate is the percentage of people who are unemployed in a given area.',
                            ],
                            [
                                'name' => 'Labor Force Participation Rate',
            
            
                                'definition' => 'Labor force participation rate is the percentage of people who are in the labor force in a given area.',
                            ],
                            [
                                'name' => 'Employment Population Ratio',
            
            
                                'definition' => 'Employment population ratio is the percentage of people who are employed in a given area.',
                            ],
                            [
                                'name' => 'Educational Attainment',
            
            
                                'definition' => 'Educational attainment is the percentage of people who have completed a certain level of education in a given area.',
                                
                            ],
                            [
                                'name' => 'Parental Employment Instability',
            
            
                                'definition' => 'Parental employment instability is the percentage of parents who are unemployed or underemployed in a given area.',
                                
                            ],
                            [
                                'name' => 'Resident Jobs',
            
            
                                'definition' => 'Industry number and percentages refer to the total and share of workers (aged 16 and over) in an industry, by the residence of the jobholder.',
                                
                            ],
                            [
                                'name' => 'Commuting',
            
            
                                'definition' => 'Commuting is the percentage of people who commute to work in a given area.',
                            ]
                        ]
                        
                    ],
                    [
                        'name' => 'Income Support',
    
    
                        'indicators' => [
                            [
                                'name' => 'SNAP (Food Stamps)',
            
            
                                'definition' => 'SNAP (Food Stamps) is the percentage of people who are receiving food stamps in a given area.',
                            ],
                            [
                                'name' => 'Public Assistance',
            
            
                                'definition' => 'Public assistance is the percentage of people who are receiving public assistance in a given area.',
                            ],
                            [
                                'name' => 'Earned Income Tax Credit Claims',
            
            
                                'definition' => 'Earned income tax credit claims is the percentage of people who are claiming the earned income tax credit in a given area.',
                            ],
                            [
                                'name' => 'Child and Dependent Care Credit',
            
            
                                'definition' => 'Child and dependent care credit is the percentage of people who are claiming the child and dependent care credit in a given area.',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Financial Security',
    
    
                        'indicators' => [
                            [
                                'name' => 'Unbanked Households',
            
            
                                'definition' => 'Unbanked households are the percentage of households that do not have a bank account in a given area.',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Economic Resources',
    
    
                        'indicators' =>[
                            [
                                'name' => 'Banks/Credit Unions',
            
            
                                'definition' => 'Unbanked households are the percentage of households that do not have a bank account in a given area.',
                            ]
                        ]
                    ],
                ],
            ],
            [
                'name' => 'Housing and Homelessness',
                'subcategories' => [
                    [
                        'name' => 'Housing Availability and Affordability',
    
    
                        'indicators' => [
                            [
                                'name' => 'Monthly Rent',
            
            
                                'definition' => 'Monthly rent is the average monthly rent in a given area.',
                            ],
                            [
                                'name' => 'Median Monthly Rent',
            
            
                                'definition' => 'Median monthly rent is the median monthly rent in a given area.',
                            ],
                            [
                                'name' => 'Severe Rent Burden',
            
            
                                'definition' => 'Severe rent burden is the percentage of people who are paying more than 50% of their income on rent in a given area.',
                            ],
                            [
                                'name' => 'Median Rent Burden',
            
            
                                'definition' => 'Median rent burden is the median percentage of income that people are paying on rent in a given area.',
                            ],
                            [
                                'name' => 'Home Ownership',
            
            
                                'definition' => 'Home ownership is the percentage of people who own their home in a given area.',
                            ],
                            [
                                'name' => 'Occupied Public Housing and Rent Regulated Units',
            
            
                                'definition' => 'Occupied public housing and rent regulated units is the percentage of people who are living in public housing or rent regulated units in a given area.',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Housing Conditions',
    
    
                        'indicators' =>[
                            [
                                'name' => 'Fair to Poor Housing',
            
            
                                'definition' => 'Fair to poor housing is the percentage of people who are living in fair to poor housing conditions in a given area.',
                            ],
                            [
                                'name' => 'Overcrowded Rental Housing',
            
            
                                'definition' => 'Overcrowded rental housing is the percentage of people who are living in overcrowded rental housing conditions in a given area.',
                            ],
                            [
                                'name' => 'Maintenance Deficiencies',
            
            
                                'definition' => 'Maintenance deficiencies is the percentage of people who are living in housing with maintenance deficiencies in a given area.',
                            ],
                            [
                                'name' => 'Residential Units',
            
            
                                'definition' => 'Residential units is the percentage of people who are living in residential units in a given area.',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Homelessness',
    
    
                        'indicators' =>[
                            [
                                'name' => 'Average Daily Shelter Population',
            
            
                                'definition' => 'Average daily shelter population is the average number of people who are living in shelters in a given area.',
                            ],
                            [
                                'name' => 'Families with Children Entering Homeless Shelters (Legacy Data)',
            
            
                                'definition' => 'Families with children entering homeless shelters is the percentage of families with children who are entering homeless shelters in a given area.',
                            ],
                            [
                                'name' => 'Families with Children in Homeless Shelters (Legacy Data)',
            
            
                                'definition' => 'Families with children in homeless shelters is the percentage of families with children who are living in homeless shelters in a given area.',
                            ],
                            [
                                'name' => 'Residential Evictions',
            
            
                                'definition' => 'Residential evictions is the percentage of people who are being evicted from their homes in a given area.',
                            ],
                            [
                                'name' => 'Homeless Shelter Population (Legacy Data)',
            
            
                                'definition' => 'Homeless shelter population is the percentage of people who are living in homeless shelters in a given area.',
                            ],
                            [
                                'name' => 'Tier II Homeless Shelter Capacity',
            
            
                                'definition' => 'Tier II homeless shelter capacity is the percentage of people who are living in Tier II homeless shelters in a given area.',
                            ],
                            [
                                'name' => 'Average Length of Stay in Shelter',
            
            
                                'definition' => 'Average length of stay in shelter is the average number of days that people are living in shelters in a given area.',
                            ]
                        ]
                    ]
                ],
            ],
            [
                'name' => 'Health and Mental Health',
                'subcategories' => [
                    [
                        'name' => 'Coronavirus (COVID-19)',
    
    
                        'indicators' => [
                            [
                                'name' => 'COVID-19 Cases',
            
            
                                'definition' => 'People who are infected with COVID-19 in a given area.',
                            ],
                            [
                                'name' => 'COVID-19 Hospitalizations',
            
            
                                'definition' => 'People who are hospitalized with COVID-19 in a given area.',
                            ],
                            [
                                'name' => 'COVID-19 Deaths',
            
            
                                'definition' => 'People who have died from COVID-19 in a given area.',
                            ]
                        ]
                    ],
                    [
                        'name' => 'General Health',
    
    
                        'indicators' => [
                            [
                                'name' => 'Life Expectancy',
            
            
                                'definition' => 'Life expectancy is the average number of years that people are expected to live in a given area.',
                            ],
                            [
                                'name' => 'Self-Reported Health Status',
            
            
                                'definition' => 'Self-reported health status is the percentage of people who report their health as good, fair, or poor in a given area.',
                            ],
                            [
                                'name' => 'Personal Doctor or PCP',
            
            
                                'definition' => 'The number and percentage of adults that reported having a personal doctor or Primary Care Physician (PCP). Numbers represent the estimated number of adults aged 18 and over, and is unadjusted for age and rounded to the nearest 1,000. All percentages are age adjusted.',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Infant and Maternal Health',
    
    
                        'indicators' => [
                            [
                                'name' => 'Live Births',
            
            
                                'definition' => 'Live births is the number of live births in a given area.',
                            ],
                            [
                                'name' => 'Maternal Mortality',
            
            
                                'definition' => 'Maternal mortality is the number of maternal deaths in a given area.',
                            ],
                            [
                                'name' => 'Low Birthweight Babies',
            
            
                                'definition' => 'Low birthweight babies is the number of babies who are born with a low birthweight in a given area.',
                            ],
                            [
                                'name' => 'Infant Mortality',
            
            
                                'definition' => 'Infant mortality is the number of infant deaths in a given area.',
                            ],
                            [
                                'name' => 'Late or No Prenatal Care',
            
            
                                'definition' => 'Late or no prenatal care is the percentage',
                            ],
                            [
                                'name' => 'Preterm Births',
            
            
                                'definition' => 'Preterm births is the number of preterm births in a given area.',
                            ],
                            [
                                'name' => 'Exclusive Breastfeeding',
            
            
                                'definition' => 'Exclusive breastfeeding is the percentage of babies who are exclusively breastfed in a given area.',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Asthma',
    
    
                        'indicators' =>[
                            [
                                'name' => 'Asthma Emergency Department Visits',
            
            
                                'definition' => 'Asthma emergency department visits is the number of asthma emergency department visits in a given area.',
                            ],
                            [
                                'name' => 'Asthma Hospitalizations',
            
            
                                'definition' => 'Asthma hospitalizations is the number of asthma hospitalizations in a given area.',
                            ],
                            
                        ]
                    ],
                    [
                        'name' => 'Insurance',
    
    
                        'indicators' => [
                            [
                                'name' => 'Uninsured',
            
            
                                'definition' => 'Uninsured is the percentage of people who are uninsured in a given area.',
                            ],
                            [
                                'name' => 'Children Covered by Medicaid',
            
            
                                'definition' => 'Children covered by Medicaid is the percentage of children who are covered by Medicaid in a given area.',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Nutrition and Food Security',
    
    
                        'indicators' => [
                            [
                                'name' => 'Obesity Among Public Elementary and Middle School Students',
            
            
                                'definition' => 'Obesity among public elementary and middle school students is the percentage of public elementary and middle school students who are obese in a given area.',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Mental Health and Other Services',
    
    
                        'indicators' => [
                            [
                                'name' => 'Children with a Disability',
            
            
                                'definition' => 'Children with a disability is the percentage of children who have a disability in a given area.',
                            ],
                            [
                                'name' => 'Youth Depression',
            
            
                                'definition' => 'Youth depression is the percentage of youth who have depression in a given area.',
                            ],
                            [
                                'name' => 'Youth Attempted Suicide',
            
            
                                'definition' => 'The percentage of students who reported attempting suicide one or more times in the past 12 months.',
                            ],
                            [
                                'name' => 'Children Receiving Mental Health Services',
            
            
                                'definition' => 'The PCS is conducted every two years. Figures reflect data collected from all programs licensed or funded by the NYS Office of Mental Health (OMH) for a specified one-week period in each year.',
                            ],                
                        ]
                    ],
                    [
                        'name' => 'Early Intervention',
    
    
                        'indicators' => [
                            [
                                'name' => 'Referrals to Early Intervention',
            
            
                                'definition' => 'Referrals include new and re-referrals.',
                            ],
                            [
                                'name' => 'Children Receiving Early Intervention Services',
            
            
                                'definition' => 'Children receiving early intervention services are defined as having had an authorization for a general service (OT, PT, speech and/or special instruction) during the fiscal year; this definition excludes children who received only service coordination and/or evaluation.',
                            ],
                            [
                                'name' => 'Early Intervention Progress Statistics by Race/Ethnicity',
            
            
                                'definition' => 'Eearly Intervention service progression by race/ethnicity. Only includes new referrals.',
                            ],
                            [
                                'name' => 'Average Drop off by Race/Ethnicity',
            
            
                                'definition' => 'The average drop off is the percentage decline in the average number of children progressing through the Early Intervention program from 2016 to 2018, by race and United Hospital Fund district.',
                            ],
                            [
                                'name' => 'Timeliness of Initial IFSP Meetings from Referral Date',
            
            
                                'definition' => 'The Individualized Family Service Plan (IFSP) is a written plan that identifies the EI services the child and family will receive. Federal law requires that the initial IFSP meeting (during which the IFSP is developed) must be convened within 45 calendar days of the date the child is referred to the EI Program.',
                            ],
                            [
                                'name' => 'Timeliness of Service Receipt Within 30 Days of the IFSP Meeting',
            
            
                                'definition' => 'The number of children with IFSPs who were, during the reporting period, receiving the early intervention authorized services (in full, in part or none) as recommended in their IFSPs within 30 days of the meeting.',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Environment',
    
    
                        'indicators' => [
                            [
                                'name' => 'Children Under 6 Years with Elevated Blood Lead Levels (BLL)',
            
            
                                'definition' => 'Number of children less than 6 years old tested in a given year with blood lead levels of 5 mcg/dL or 10 mcg/dL or greater.',
                            ],
                            [
                                'name' => 'Walking Distance to a Park',
            
            
                                'definition' => 'The percentage of the population that potentially live within walking distance to a park. Walking distance to a park is defined as 1/4-mile or less to entrances of smaller sites, such as sitting areas and playgrounds, and 1/2-mile or less to entrances of larger parks. Parks and open space that are not under the NYC Parks Department jurisdiction are also included.',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Health Resources',
    
    
                        'indicators' =>[
                            [
                                'name' => 'Large Food Retail',
            
            
                                'definition' => 'Rate of adults per large food retail represents the total population per one large food retail (10,000 square feet or larger) in a given geography.',
                            ],
                            [
                                'name' => 'Medical Facilities',
            
            
                                'definition' => 'Rate of adults per medical facility represents the total population per one medical facility which can include hospitals, diagnostic and treatment centers and health clinics in a given geography.',
                            ],
                        ]
                    ]

                ],
            ],
            [
                'name' => 'Early Care and Education',
                'subcategories' => [
                    [
                        'name' => 'Population of Children Under 5',
    
    
                        'indicators' =>[
                            [
                                'name' => 'Population of Children Under 5',
            
            
                                'definition' => 'Population of children under 5 is the number of children under 5 years old in a given area.',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Enrollment',
    
    
                        'indicators' => [
                            [
                                'name' => 'Early Education Enrollment in Private and Public Programs',
            
            
                                'definition' => '3-and 4-year-olds enrolled in early education programs. Percents refer to the share of all 3 and 4 year olds in a geography.',
                            ],
                            [
                                'name' => 'Enrollment in Publicly Funded Care for Children Under 5',
            
            
                                'definition' => 'Publicly funded care for children under five-year-old captures seats contracted by the Department of Education (NYC Public Schools), including Pre-K, 3-K and infant and toddler seats, as well as vouchers from Human Resources Administration (HRA) and Administration for Children’s Services (ACS).',
                            ],
                            [
                                'name' => 'Enrollment in Contracted Care for Children Under 5',
            
            
                                'definition' => 'Contracted care for children under five-year-old includes: contracted subsidized EarlyLearn and NYC Head Start (during 2019/2020 transitioned from the Administration for Children’s Services to the Department of Education) and universal programs administered by the Department of Education to provide Pre-K and 3-K for All.',
                            ],
                            [
                                'name' => 'Enrollment in Subsidized Care for All Children',
            
            
                                'definition' => 'Subsidized care for all children includes contracted subsidized EarlyLearn and NYC Head Start (during 2019/2020 transitioned from the Administration for Children’s Services to the Department of Education); vouchers from Human Resources Administration (HRA) and Administration for Children’s Services (ACS).',
                            ],
                            [
                                'name' => 'Enrollment in EarlyLearn Contracted Care for All Children',
            
            
                                'definition' => 'EarlyLearn contracted care also includes NYC Head Start and 3-K and Pre-K seats in these settings (during 2019/2020 transitioned from the Administration for Children’s Services to the Department of Education).',
                            ],
                            [
                                'name' => 'Enrollment in Pre-K for All',
            
            
                                'definition' => 'Pre-K for All is a universal program administered by the Department of Education to provide Pre-K and 3-K for All.',
                            ],
                            [
                                'name' => 'Enrollment in 3-K for All',
            
            
                                'definition' => '3-K for All is a universal program administered by the Department of Education to provide Pre-K and 3-K for All.',
                            ],
                            [
                                'name' => 'Contracted Enrollment by Length of Care by Age Group',
            
            
                                'definition' => 'Length of care by age group refers to enrollment in contracted system which includes Early Learn, 3-K and Pre-K for All provided in center-based settings, licensed family settings and schools (schools include public, charter, special education schools, and standalone Pre-K Centers).Full-Day/Year-Round: 8 or 10 hours a day; 225 or 260 days a year. School-Day/School Year: 6 hours and 20 minutes a day; 180 days a year.',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Utilization',
    
    
                        'indicators' => [
                            [
                                'name' => 'Child Care Voucher Utilization for Children Under 5',
            
            
                                'definition' => 'Families eligible for vouchers are either receiving Cash Assistance from the Human Resources Administration (HRA) or have an ACS-issued voucher. Vouchers act like an electronic coupon that a family can take to any child care provider they choose: center-based, licensed family child care, or unlicensed informal care (family, friend or neighbor).',
                            ],
                            [
                                'name' => 'Child Care Voucher Utilization for All Children',
            
            
                                'definition' => 'Families eligible for vouchers are either receiving mandated vouchers for families on Cash Assistance from the Human Resources Administration (HRA) or have an ACS-issued voucher (including non-mandated low-income vouchers and child welfare vouchers). Vouchers act like an electronic coupon that a family can take to any child care provider they choose: center-based, licensed family child care, or unlicensed informal care (family, friend or neighbor). Vouchers can be used for children ages six weeks to 13 years old.',
                            ],
                            [
                                'name' => 'Unmet Need for Publicly Funded Child Care',
            
            
                                'definition' => 'Population: estimated population of infants.Income Eligible = number of infants in households below 200% of the Federal Poverty Level (around $50,000 for a family of 4). Percents refer to the percent of all infants (Population).Enrolled in Publicly Funded Child Care = number of infants enrolled in contracted subsidized EarlyLearn and NYC Head Start programs and infants that utilize vouchers from Human Resources Administration (HRA) and Administration for Children’s Services (ACS). This includes licensed centers and family day care and informal settings.Percents refer to the percent of all infants in households below 200% of the FPL (Income Eligible).Not Enrolled in Publicly Funded Child Care = number of infants in households below 200% of the FPL (Income Eligible) not enrolled in Publicly Funded Child Care. Percents refer to the percent of all infants in households below 200% of the FPL (Income Eligible).',
                            ],
                            [
                                'name' => 'Capacity in Contracted Care for Children Under 5',
            
            
                                'definition' => 'Contracted care for children under five-year-old includes: contracted subsidized EarlyLearn and NYC Head Start and universal programs to provide Pre-K and 3-K for All.',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Affordability',
    
    
                        'indicators' => [
                            [
                                'name' => 'Cost Burden for Infant/Toddler Child Care',
            
            
                                'definition' => 'Child care cost burden refers to the median cost of infant/toddler child care as a percentage of median household income in the past year for families with children under five.',
                            ],
                            [
                                'name' => 'Infant/Toddler Child Care Affordability',
            
            
                                'definition' => 'Child care affordability data represents families with children under 5 who pay less than 7% of their annual income on the estimated cost of child care. The threshold for child care affordability is based on the U.S. Department of Health and Human Services’ Child Care and Development Fund benchmark that family copayments should not exceed 7% of a family’s income.',
                            ],
                            [
                                'name' => 'Preschool (Age 3-5) Child Care Cost Burden',
            
            
                                'definition' => 'Preschool child care cost burden refers to the median cost of care for children ages 3-5 as a percentage of median household income in the past year for families with children under five.',
                            ],
                            [
                                'name' => 'Preschool (Age 3-5) Child Care Affordability',
            
            
                                'definition' => 'Child care affordability data represents families with children under 5 who pay less than 7% of their annual income on the estimated cost of child care. The threshold for child care affordability is based on the U.S. Department of Health and Human Services’ Child Care and Development Fund benchmark that family copayments should not exceed 7% of a family’s income.',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Quality',
    
    
                        'indicators' => [
                            [
                                'name' => 'Pre-K Learning Environment Quality',
            
            
                                'definition' => 'Average Early Childhood Education Rating System (ECERS) Observation Score',
                            ],
                        ]
                    ]
                ],
            ],
            [
                'name' => 'Education',
                'subcategories' => [
                    [
                        'name' => 'Student Characteristics',
    
    
                        'indicators' =>[
                            [
                                'name' => 'Enrollment',
            
            
                                'definition' => 'Enrollment (official register) in New York City public schools.',
                            ],
                            [
                                'name' => 'English Language Learners',
            
            
                                'definition' => 'Students designated as English language learners.',
                            ],
                            [
                                'name' => 'Students Living in Temporary Housing',
            
            
                                'definition' => 'Public school students (including charter schools) who identified as homeless anytime during the school year, even if they are no longer experiencing homelessness at the end of the school year.',
                            ],
                            [
                                'name' => 'Students with Disabilities (IEP)',
            
            
                                'definition' => 'Students with disabilities are defined as any child receiving an IEP as of the end of the school year.',
                            ],
                            [
                                'name' => 'Student Racial/Ethnic Diversity',
            
            
                                'definition' => 'Diversity refers to the racial/ethnic makeup of students at schools within a specific geography, using the DOE\'s categories of Asian, Black, Hispanic, White, and Multiple Race Categories Not Represented.',
                            ],
                            [
                                'name' => 'Student Economic Status',
            
            
                                'definition' => '"Poverty" counts are based on the number of students with families who have qualified for free or reduced price lunch, or are eligible for Human Resources Administration (HRA) benefits. ',
                            ],
                        ]
                    ],
                    [
                        'name' => 'School Characteristics',
                        'indicators' => [
                            [
                                'name' => 'Attendance',
                                'definition' => 'Data prior to 2014 represent the average school year attendance rate, while data from 2014 onward represent the total number of days students were present in school divided by the total number of days all students were absent and present. Therefore, data from 2014 and onward should not be compared to prior years.',
                            ],
                            [
                                'name' => 'Chronic Absenteeism',
                                'definition' => 'Students are considered chronically absent if they have an attendance rate less than 90 percent (i.e. students who are absent 10 percent or more of the total days). In order to be included in chronic absenteeism calculations, students must be enrolled for at least 20 days (regardless of whether present or absent).',
                            ],
                            [
                                'name' => 'School Utilization and Overcrowding',
                                'definition' => 'Utilization Rate is enrollment shown as a percentage of capacity. Overcrowded Schools is the percentage of schools in a geography where enrollment exceeds capacity.',
                            ],
                            [
                                'name' => 'Teacher Certification',
                                'definition' => 'Teaching Out of Certification refers to the number of individuals teaching out of their subject/field of certification. No Valid Certificate refers to the number of individuals without valid teaching certificates.',
                            ],
                            [
                                'name' => 'Teacher Experience',
                                'definition' => 'Fewer Than 4 Years Experience refers to the number of teachers with fewer than four years of teaching experience for school years 2018 through 2020; and refers to the number of teachers with fewer than three years of teaching experience prior to 2018. Data for these periods should be compared with caution.',
                            ],
                            [
                                'name' => 'School Staff Demographics',
                                'definition' => 'Teachers include staff in a teacher title. Leadership includes Principals and Assistant Principals.Other staff includes any other pedagogic staff (aside from teachers and leadership), paraprofessionals, and therapists. ',
                            ],
                            [
                                'name' => 'School District Racial/Ethnic Representativeness',
                                'definition' => 'Following recommendations laid out by the Mayor\'s School Diversity Advisory Group (SDAG), this data explores the extent to which New York City public schools are racially/ethnically representative of their school district. ',
                            ],
                            [
                                'name' => 'Student Economic Need Index',
                                'definition' => 'The school’s Economic Need Index is the average of its students’ Economic Need Values. The Economic Need Index (ENI) estimates the percentage of students facing economic hardship.',
                            ],
                            [
                                'name' => 'School Meals',
                                'definition' => 'Breakfast ADP: the average daily number of breakfasts served in the cafeteria by the department before the school day begins. Lunch ADP: the average daily number of lunches served by the department. After School Snacks ADP: the average daily number of after school snacks served by the department. After School Supper ADP: the average daily number of after school suppers served by the department.',
                            ],
                            [
                                'name' => 'Student Discipline',
                                'definition' => 'Data for total student discipline include: principal suspensions, lasting 1 to 5 days; superintendent suspensions, which exceed 5 days; and removals of students from the classroom by a teacher. Rates refer to the number of disciplinary actions per 1,000 students enrolled in the corresponding school year.',
                            ],
                        ]
                    
                    ],
                    [
                        'name' => 'Student Performance Metrics',
                        'indicators' => [
                            [
                                'name' => 'High School ELA Proficiency',
                                'definition' => 'Public high school students meeting proficiency standards in English Language Arts (ELA) after 4 years of instruction.',
                            ],
                            [
                                'name' => 'High School Math Proficiency',
                                'definition' => 'Public high school students meeting proficiency standards in Math after 4 years of instruction.',
                            ],
                            [
                                'name' => 'Reading Test Scores (3rd through 8th Grades)',
                                'definition' => 'Number and Percent Passing refer to third through eighth graders who performed at or above grade level (scoring at level 3 or 4) on New York State standardized reading tests. Mean Score is the average score for the geography and/or breakdown selected. Percent Testing is the share of students who took the test in 2021, which was made optional to students amid the COVID-19 pandemic.',
                            ],
                            [
                                'name' => 'Math Test Scores (3rd through 8th Grades)',
                                'definition' => 'Number and Percent Passing refer to third through eighth graders who performed at or above grade level (scoring at level 3 or 4) on New York State standardized reading tests. Mean Score is the average score for the geography and/or breakdown selected. Percent Testing is the share of students who took the test in 2021, which was made optional to students amid the COVID-19 pandemic.',
                            ],
                        ]
    
    
                    ],
                    [
                        'name' => 'Graduation Outcomes',
                        'indicators' => [
                            [
                                'name' => 'Graduation Rate',
                                'definition' => 'New York City public high school students who graduated as of June after four years or six years of instruction. Six-year graduation rate data represent graduation rates for the cohort entering 6 years prior to the current school year (e.g. the 2019 six-year graduation rate reflect data for the 2013 cohort).',
                            ],
                            [
                                'name' => 'Dropout Rate',
                                'definition' => 'New York City public high school students who had dropped out as of June after four years or six years of instruction. Six-year dropout rate data represent dropout rates for the cohort entering 6 years prior to the current school year (e.g. the 2019 six-year dropout rate reflect data for the 2013 cohort).',
                            ],
                            [
                                'name' => 'College Readiness',
                                'definition' => 'The college readiness rate shows the percentage of students in the school’s four-year cohort who, by the August after their fourth year in high school, graduated with a Local Diploma or higher and met CUNY’s standards for college readiness in English and mathematics. For the 2019-20 data, this metric evaluates students who first entered high school during the 2016-17 school year / “Class of 2020”.',
                            ],
                            [
                                'name' => 'Postsecondary Enrollment',
                                'definition' => 'The postsecondary enrollment rate shows the percentage of students who graduated and enrolled in a two- or four-year college, vocational program, approved apprenticeship or public service within 6 or 18 months of their scheduled graduation date. For the 2019-20 data, the 6 month rate evaluates students who first entered high school during the 2015-16 school year / “Class of 2019”; the 18 month rate evaluates students who first entered high school during the 2014-15 school year / “Class of 2018”.',
                            ],
                            [
                                'name' => 'College Persistence',
                                'definition' => 'The college persistence rate shows the percentage of students in the six-year cohort who graduated, enrolled, and persisted in college through the beginning of their third semester, within six years of starting high school. To count as having persisted, a student must have enrolled in college for three consecutive semesters. For the 2019-20 data, this metric evaluates students who first entered high school during the 2014-15 school year / “Class of 2018”.',
                            ],
                        ]
    
    
                    ]
                ],
            ],
            [
                'name' => 'Youth and Juvenile Justice',
                'subcategories' => [
                    [
                        'name' => 'Teen Births',
                        'indicators' => [
                            [
                                'name' => 'Teen Births',
                                'definition' => 'Number of births to teenage mothers younger than 20 years old, per 1,000 teenage girls aged 15 to 19. Citywide births represent the total number of teen births to occur in New York City. Borough and community district totals represent teen births to NYC residents only.',
                            ],
                        ]
    
    
                    ],
                    [
                        'name' => 'Teen Employment and Idleness',
                        'indicators' =>[
                            [
                                'name' => 'Teen Idleness (16 to 19 Years)',
                                'definition' => 'Teens 16 to 19 years who are not in school and not in the labor force. This excludes teens who serve in the armed forces. Percents refer to the percent of all teenagers 16 to 19 years.',
                            ],
                            [
                                'name' => 'Teen Unemployment (16 to 19 Years)',
                                'definition' => 'Teens 16 to 19 years actively seeking employment who are unemployed.',
                            ],
                            [
                                'name' => 'Youth Unemployment (20 to 24 Years)',
                                'definition' => 'Youth 20 to 24 years who are without a job and were actively seeking employment within the last four weeks.',
                            ],
                            [
                                'name' => 'Disconnected Youth (16 to 24 Years)',
                                'definition' => 'Teens and youth 16 to 24 years who are not in school and not working. Percents refer to the percent of all teenagers and youth 16 to 24 years.',
                            ],
                        ]
    
    
                    ],
                    [
                        'name' => 'Juvenile Justice',
                        'indicators' => [
                            [
                                'name' => 'Juvenile Arrests (Under 16 Years)',
                                'definition' => 'Arrests of children under 16 years.',
                            ],
                            [
                                'name' => 'Arrests of 16 and 17 Year Olds',
                                'definition' => 'Arrest counts for violent felony, non-violent felony, misdemeanor, and VTL offenses.',
                            ],
                            [
                                'name' => 'Minor Arrests (Under 18 Years Old)',
                                'definition' => 'Arrests of children under 18 years.',
                            ],
                            [
                                'name' => 'Youth Arrests (18 to 24 Years)',
                                'definition' => 'Arrests of youth 18 to 24 years of age.',
                            ],
                            [
                                'name' => 'Admissions to Juvenile Detention (Calendar Year)',
                                'definition' => 'Unique youth admitted to juvenile detention during the calendar year. ',
                            ],
                            [
                                'name' => 'Close to Home Placements',
                                'definition' => 'The annual number of Close to Home admissions by calendar year.',
                            ],
                            [
                                'name' => 'Adolescents in Adult Jails',
                                'definition' => 'The total number of adolescent admissions and average daily adolescent population in adult city jails. Adolescents are 16 through 18 years of age.',
                            ]
                        ]
                    ]
                ],
            ],
            [
                'name' => 'Child Welfare and Community Safety',
                'subcategories' =>[
                    [
                        'name' => 'Abuse and Neglect',
                        'indicators' => [
                            [
                                'name' => 'Child Abuse and Neglect Investigations',
                                'definition' => 'Consolidated investigations of child abuse and neglect.',
                            ],
                            [
                                'name' => 'Children in Child Abuse and Neglect Investigations',
                                'definition' => 'Children involved in investigations of abuse or neglect by community district of residence. The number of children is the sum of children in investigations during the year, such that there are duplicate counts for children involved in more than one investigation during the year. Rates refer to the number of children in investigations of abuse or neglect per 1,000 children under 18 years.',
                            ],
                            [
                                'name' => 'Indicated Child Abuse and Neglect Investigations',
                                'definition' => 'Investigations of child abuse and neglect in which the city’s child welfare agency, the Administration for Children’s Services (ACS), found credible evidence of abuse or neglect. Indicated investigations exclude investigations that were suspended, withdrawn, or open at the time of analysis.',
                            ],
                            [
                                'name' => 'Victimization Rate',
                                'definition' => 'Victimization Rate is the number of distinct children under 18 with indicated reports of abuse or neglect per 1,000 children under 18.',
                            ]
                        ]
    
    
                    ],
                    [
                        'name' => 'Prevention',
                        'indicators' => [
                            [
                                'name' => 'New Preventive Services Cases',
                                'definition' => 'New child welfare preventative cases opened within the year by borough and community district of case. This does not include cases transferred from one provider to another.',
                            ],
                            [
                                'name' => 'Children Served in Preventive Services',
                                'definition' => 'The annual number of unique children who received child welfare preventive services in a given year by community district of residence.',
                            ]
                        ]
    
    
                    ],
                    [
                        'name' => 'Foster Care',
                        'indicators' =>[
                            [
                                'name' => 'Foster Care Placements',
                                'definition' => 'Children placed into foster care.',
                            ],
                            [
                                'name' => 'Foster Care Population',
                                'definition' => 'Children in foster care by community district of foster care placement, as of the last day of the calendar year.',
                            ]
                        ]
    
    
                    ],
                    [
                        'name' => 'Domestic Violence',
                        'indicators' => [
                            [
                                'name' => 'Domestic Incident Reports',
                                'definition' => 'The number of domestic violence incident reports that were filed by the New York City Police Department. Rates refer to the number of domestic violence incident reports per 1,000 households in the geography.',
                            ],
                            [
                                'name' => 'Domestic Felony Assaults',
                                'definition' => 'The number of felony assault offenses involving intimate partners or family members that were filed by the New York City Police Department. Rates refer to the number of domestic violence incident reports per 1,000 households in the geography.',
                            ],
                            [
                                'name' => 'Domestic Rape Offenses',
                                'definition' => 'The number of felony rapes involving intimate partners or family members that were filed by the New York City Police Department. This does not include rape offenses between non-intimate partners and non-family members. Rates refer to the number of domestic violence incident reports per 1,000 households in the geography.',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Community Safety',
                        'indicators' => [
                            [
                                'name' => 'Arrests',
                                'definition' => 'Arrests by offense type.',
                            ],
                            [
                                'name' => 'Criminal Justice System Involvement',
                                'definition' => 'The number and share of adults aged 18 and older who report involvement in the criminal justice system. "Personal" involvement includes adults who report spending any amount of time in a juvenile or adult correctional facility, jail, prison, or detention center OR having been under probation or parole supervision. "Familial" involvement includes adults who report that an immediate family member such as a spouse or partner, child, sibling, or parent has spent time in any of these facilities or conditions.',
                            ],
                            [
                                'name' => 'Police Misconduct',
                                'definition' => 'Total refers to the total number of complaints received by the Civilian Complaint Review Board by precinct where the incident occurred. A complaint is defined as any incident within the Agency’s jurisdiction that falls into one or more of the following categories of misconduct specified by the New York City Charter: Force, Abuse of Authority, Discourtesy, and Offensive Language, collectively known as “FADO”.  Complaints may contain more than one allegation.',
                            ],
                            [
                                'name' => 'Reported Felonies',
                                'definition' => 'The number of violent felony crimes reported per 1,000 residents.',
                            ],
                            [
                                'name' => 'Motor Vehicle Injuries and Fatalities',
                                'definition' => 'The total number of motor vehicle crash events that result in injury and/or death to pedestrians and cyclists, by the location where the crash occurred.',
                            ],
                            [
                                'name' => 'Community Trust',
                                'definition' => 'Share of adults ages 18 and older who report they strongly agree or somewhat agree with the following statement: "People in your neighborhood are willing to help their neighbors."',
                            ],
                            [
                                'name' => 'Street and Sidewalk Cleanliness',
                                'definition' => 'The percent of streets that are rated "acceptably clean" by the Mayor\'s Office of Operations.',
                            ]
                        ]
    
    
                    ]

                ]
            ]
            
        ];
        

        foreach ($categories as $category) {
            
            $category_record = Category::create($category);

            $subcategories = $category['subcategories'];
            
            foreach ($subcategories as $subcategory) {
                
                $subcategory['parent_id'] = $category_record->id;
                
                $subcategory_record = Category::create($subcategory);

                $indicators = $subcategory['indicators'] ?? [];

                foreach ($indicators as $indicator) {
                    
                    $indicator['category_id'] =  $subcategory_record->id;

                    Indicator::create($indicator);
                }
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
