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
            'Age Group' => [
                '0 through 5 Years',
                'All Ages',
                'Infant',
                'Toddler',
                'Infant/Toddler',
                'Preschool',
                'Pre-School',
                'School Age',
                'Under 5 Years',
                '5 to 9 Years',
                '10 to 14 Years',
                '15 to 17 Years',
                '6 to 17 Years',
                '0 to 5 years',
                '6 to 13 years',
                '14 to 17 years',
                'Teens 15 to 19 Years',
                'Youth 18 to 24 Years',
                'Children Under 18 Years',
                '18 Years and Older',
                'Adults 18 to 64 Years',
                'Adults 18 Years and Older',
                'Adults 25 Years and Older',
                'Under 13 Years',
                '13 to 15 Years',
                '16 Years and Older',
                '0 to 5 months',
                '6 to 11 months',
                '12 to 17 months',
                '18 to 23 months',
                '24 to 29 months',
                '30 plus months',
                'Ages 0-5',
                'Ages 6-11',
                'Ages 12 and Older',
                'Ages 12-13',
                'Ages 14-15',
                'Age 16',
                'Age 17',
                'Age 18',
                'Ages 18 and Over',
                'Ages 7-12',
                'Ages 13-15',
                'Ages 16 and Older',
                '0 to 14 years',
                '0 to 4 years',
                '5 to 14 years',
                'Children Under 19 Years',
                'Adults 19 Years and Older',
                '3-year-old',
                '4-year-old',
                '3-and 4-year-old',
                '5 to 17 Years',
                '0 to 17 Years',
                'Less than 1 Year',
                '1 to 5 Years',
                '6 to 11 Years',
                '12 Years and Older',
                '0-17 Years',
                '18-44 Years',
                '45-64 Years',
                '65-75 Years',
                '75 and Older',

            ],
            'School Level' => [
                'All Levels',
                'High Schools',
                'Elementary Schools',
                'Intermediate Schools',
                'Elementary/Intermediate Schools',
                'PK in K-12 Schools'
            ],
            'Race/Ethnicity' => [
                'All Races and Ethnicities',
                'Asian',
                'Black',
                'Latino',
                'White',
                'Hispanic or Latino',
                'Other',
                'Puerto Rican',
                'Other Hispanic',
                'Hispanic',
                'Multi-Racial',
                'All Races',
                'Asian and Pacific Islander',
                'White, Non-Latinx',
                'Black, Non-Latinx',
                'Latinx',
                'Asian, Non-Latinx',
                'Other/Unknown',
                'Asian/PI/NA',
                'Latine',
                'Combination or Another Race',
                'Native Americans'
            ],
            'English Language Proficiency' => [
                'All Students',
                'English Language Learners',
                'English Language Proficient',
                'Former English Language Learners'
            ],
            'Gender' => [
                'All Students',
                'Female',
                'Male'
            ],
            'Students with Disabilities' => [
                'All Students',
                'General Education',
                'Students with Disabilities',
                'Students without Disabilities'
            ],
            'Income Level' => [
                'Under $15,000',
                '$15,000 to $24,999',
                '$25,000 to $34,999',
                '$25,000 to $34,999',
                '$35,000 to $49,999',
                '$50,000 to $74,999',
                '$75,000 to $99,999',
                '$100,000 to $199,999',
                '$200,000 or more'
            ],
            'Felony Type' => [
                'All Felonies',
                'Violent Felonies'
            ],
            'Household Type' => [
                'All Households',
                'Families',
                'Married Couple Families',
                'Married Couple Families with Children',
                'Families with Children',
                'Families without Children',
                'Married Couples',
                'Single Parents',
                'Single Mothers',
                'Single Fathers',
                'Grandparents',
                'Other',
                'Family Households with Children',
                'Family Households without Children',
                'Non-family Households'
            ],
            'Economic Status' => [
                'Economically Disadvantaged',
                'Not Economically Disadvantaged'
            ],
            'Rent Level' => [
                'Under $500',
                '$500 to $999',
                '$1,000 to $1,499',
                '$1,500 to $1,999',
                '$2,000 to $2,499',
                '$2,500 or more',
                '$2,000 or more'
            ],
            'Type' => [
                'Not in school, not in labor force',
                'Not in school, no degree',
                'Active',
                'New',
                'EarlyLearn Contract',
                'Voucher',
                'Households',
                'Individuals',
                'Has a computer',
                'Has a computer with internet subscription',
                'Has a computer with no internet subscription',
                'No computer',
                'Any broadband',
                'Cellular only',
                'Broadband only',
                'Intimate Partner',
                'Family Member',
                'Total Suspensions',
                'Principal Suspensions',
                'Superindendent Suspensions',
                'Removals',
                'Pedestrians',
                'Cyclists',
                'ACS',
                'HRA'
            ],
            'Program Type' => [
                'All Types',
                'ACS CBO',
                'DOE CBO',
                'DOE Public Schools',
                'Family Assistance',
                'Safety Net Assistance',
                'Safety Net Converted',
                'All Program Types',
                'Public',
                'Private'
            ],
            'Data Location' => [
                'Student\'s School',
                'Student\'s Home'
            ],
            'Program Level' => [
                'All Program Levels',
                'Pre-School',
                'Kindergarten'
            ],
            'Outcome' => [
                'Graduated',
                'Still Enrolled',
                'Dropped Out'
            ],
            'Cohort' => [
                '4 Year',
                '4 Year August',
                '5 Year',
                '6 Year'
            ],
            'Recipient' => [
                'Individuals',
                'Households'
            ],
            'Cash Assistance Receipt' => [
                'Receives Cash Assistance',
                'Does not receive Cash Assistance'
            ],
            'Results',
            'Security Level' => [
                'Secure',
                'Non-Secure',
                'Both'
            ],
            'City/State Claims' => [
                'State EITC',
                'City EITC',
                'State CCTC',
                'City CCTC',
                'City CDCC',
                'State CDCC'
            ],
            'Eligibility Determination' => [
                'Found Eligible',
                'Not Found Eligible'
            ],
            'Free Lunch Eligibility' => [
                'Free Lunch Eligible',
                'Not Free Lunch Eligible',
                'Free/Reduced Lunch Eligible',
                'Not Free/Reduced Lunch Eligible'
            ],
            'Disability Status' => [
                'Students with Disabilities',
                'Students without Disabilities',
                'General Education'
            ],
            'School Type' => [
                'Traditional Public Schools',
                'Public Charter Schools'
            ],
            'Domain' => [
                'Overall',
                'Economic Security',
                'Housing',
                'Health',
                'Education',
                'Youth',
                'Family & Community'
            ],
            'Allegation by Type' => [
                'Physical Abuse',
                'Educational Neglect',
                'Lack of Medical Care',
                'Neglect',
                'Psychological Abuse',
                'Sexual Abuse',
                'Other'
            ],
            'Level of Care' => [
                'Foster Boarding Home',
                'Kinship',
                'Residential',
                'Other/ Unknown'
            ],
            'Setting' => [
                'Center-Based',
                'Home-Based',
                'Center',
                'Family',
                'Informal',
                'School',
                'DOE Pre-K Center'
            ],
            'Economic Security' => [
                'Raw Index Score',
                'Child Poverty Rate',
                'Median Income for Families with Children',
                'Parental Employment Instability'
            ],
            'Education Level' => [
                'Less than High School Degree',
                'High School Degree',
                'Some College/ Associate\'s Degree',
                'Bachelor\'s Degree',
                'Graduate Degree or Higher',
                'Some College',
                'Associate\'s Degree',
                'Bachelor\'s Degree or Higher'
            ],
            'Post-Grad Plans' => [
                '4yr College',
                '2yr College',
                'Other Post-Secondary Education',
                'Military',
                'Employment',
                'Other/ Unknown'
            ],
            'Offense Type' => [
                'Felony',
                'Misdemeanor',
                'Violent Felony',
                'Non-Violent Felony',
                'Violation',
                'Violent Felonies',
                'Non-Violent Felonies',
                'Misdemeanors'
            ],
            'Housing Type' => [
                'All Housing',
                'Rental Housing',
                'Public Housing',
                'Rent Regulated'
            ],
            'Citizenship Type' => [
                'Native Citizen',
                'Naturalized Citizen',
                'Non-Citizens'
            ],
            'Family Type' => [
                'Families with Children',
                'Adult Families',
                'Single Adults'
            ],
            'Experience' => [
                'Fewer Than 4 Years Experience',
                'Master\'s Degree or Higher'
            ],
            'Certification' => [
                'Teaching Out of Certification',
                'No Valid Certificate'
            ],
            'Utilization' => [
                'Utilization Rate',
                'Overcrowded Schools'
            ],
            'Blood Lead Level' => [
                '5 mcg/dL or greater',
                '10 mcg/dL or greater'
            ],
            'Infants' => [
                'Center',
                'Family',
                'School',
                'Informal',
                'Population',
                'Income Eligible',
                'Income Eligible Enrolled in Publicly Funded Child Care',
                'Income Eligible Not Enrolled in Publicly Funded Child Care',
                'Full Day, Year Round',
                'School Day, School Year'
            ],
            'Age Group By Setting' => [
                "Infants in Centers",
                "Infants in Family Child Care",
                "Infants in Informal Child Care",
                "Toddlers in Centers",
                "Toddlers in Family Child Care",
                "Toddlers in Informal Child Care",
                "3-year-olds in Centers",
                "3-year-olds in Family Child Care",
                "3-year-olds in Informal Child Care",
                "3-year-olds in Schools",
                "4-year-olds in Centers",
                "4-year-olds in Family Child Care",
                "4-year-olds in Informal Child Care",
                "4-year-olds in Schools"
            ],
            'Income' => [
                "Less than $20,000",
                "$20,000 to $74,999",
                "$75,000 or more"
            ],
            'Neighborhood Poverty' => [
                "Low",
                "Medium",
                "High",
                "Very High"
            ],
            'At Age 65' => [
                "Total",
                "Male",
                "Female"
            ],
            'Domestic Violence' => [
                'Intimate Partner',
                'Family Member'
            ],
            'Offender Type' => [
                'Intimate Partner',
                'Family Member'
            ],
            'Elementary Schools' => [
                "Diverse",
                "Predominantly White",
                "Predominantly Asian",
                "Predominantly White + Asian",
                "Predominantly Black",
                "Predominantly Hispanic",
                "Predominantly Black + Hispanic"
            ],
            'Middle Schools' => [
                "Diverse",
                "Predominantly White",
                "Predominantly Asian",
                "Predominantly White + Asian",
                "Predominantly Black",
                "Predominantly Hispanic",
                "Predominantly Black + Hispanic"
            ],
            'High Schools' => [
                "Diverse",
                "Predominantly White",
                "Predominantly Asian",
                "Predominantly White + Asian",
                "Predominantly Black",
                "Predominantly Hispanic",
                "Predominantly Black + Hispanic"
            ],
            'Charter Schools' => [
                "Diverse",
                "Predominantly White",
                "Predominantly Asian",
                "Predominantly White + Asian",
                "Predominantly Black",
                "Predominantly Hispanic",
                "Predominantly Black + Hispanic"
            ],
            'Industry' => [
                "Construction and Manufacturing",
                "Transportation, Warehousing, Wholesale",
                "Information, Finance, Real Estate",
                "Management and Professional Services",
                "Education",
                "Health Care and Social Services",
                "Hospitality, Accommodation, Restaurants",
                "Retail",
                "Other Services",
                "Public Administration"
            ],
            'Building Type' => [
                "One and Two Family",
                "Multi-Family Walk-Up",
                "Multi-Family Elevator",
                "Mixed Residential and Commercial"
            ],
            'Year Built' => [
                "1919 and older",
                "1920 to 1929",
                "1930 to 1946",
                "1947 to 1973",
                "1974 to 1999",
                "2000 and later"
            ],
            'Sex' => [
                'Female',
                'Male'
            ],
            'Sexual Orientation' => [
                'Heterosexual',
                'Gay, Lesbian, or Bisexual'
            ],
            'Health Status' => [
                'Excellent',
                'Very good',
                'Good',
                'Fair or poor'
            ],
            'Degree of Representation' => [
                'Representative',
                'Somewhat Representative',
                'Not Representative'
            ],
            'Length of Stay' => [
                'Less than 1 Year',
                'Between 1 and 2 Years',
                '2 or More Years'
            ],
            'Travel Time' => [
                'Under 30 minutes',
                '30 to 59 minutes',
                'An hour or more'
            ],
            'Means of Transportation' => [
                "Public Transit",
                "Driving Alone",
                "Walking",
                "Carpool, Taxi, Other",
                "Work at Home",
                "Bicycle"
            ],
            'Number of Claims' => [
                'City EITC',
                'State EITC'
            ],
            'Credit Amount Claimed' => [
                'City EITC',
                'State EITC'
            ],
            'Meal Type' => [
                "Breakfast ADP",
                "Lunch ADP",
                "After School Snacks ADP",
                "After School Suppers ADP"
            ],
            'Zip Code' => [
                'Total Tested',
                'Positive'
            ],
            'Allegation Type' => [
                "Force",
                "Abuse of Authority",
                "Discourtesy",
                "Offensive Language"
            ],
            'Poverty Group' => [
                "Low Poverty",
                "Medium Poverty",
                "High Poverty",
                "Very High Poverty"
            ],
            'Daily Count',
            'Poverty Level' => [
                "Below 100% FPL",
                "100 to 199% FPL",
                "Below 200% FPL",
                "200 to 399% FPL",
                "400% FPL and above"
            ],
            'Center Based' => [
                'All Families',
                'Married Couples',
                'Single Parents'
            ],
            'Home Based' => [
                'All Families',
                'Married Couples',
                'Single Parents'
            ],
            '4-year-olds' => [
                "Population",
                "Enrolled in Publicly Funded Child Care",
                "Not Enrolled in Publicly Funded Child Care",
                "Full Day, Year Round",
                "School Day, School Year"
            ],
            'Toddlers' => [
                "Population",
                "Income Eligible",
                "Income Eligible Enrolled in Publicly Funded Child Care",
                "Income Eligible Not Enrolled in Publicly Funded Child Care",
                "Full Day, Year Round",
                "School Day, School Year"
            ],
            '3-year-olds' => [
                "Population",
                "Enrolled in Publicly Funded Child Care",
                "Not Enrolled in Publicly Funded Child Care",
                "Full Day, Year Round",
                "School Day, School Year"
            ],
            'Teachers' => [
                "Total",
                "Asian",
                "Black/African American",
                "Hispanic",
                "White"
            ],
            'Leadership' => [
                "Total",
                "Asian",
                "Black/African American",
                "Hispanic",
                "White"
            ],
            'Other Staff' => [
                "Total",
                "Asian",
                "Black/African American",
                "Hispanic",
                "White"
            ],
            'Referred' => [
                "All Races",
                "White, Non-Latinx",
                "Black, Non-Latinx",
                "Latinx",
                "Asian, Non-Latinx"
            ],
            'Evaluated' => [
                "All Races",
                "White, Non-Latinx",
                "Black, Non-Latinx",
                "Latinx",
                "Asian, Non-Latinx"
            ],
            'Eligible' => [
                "All Races",
                "White, Non-Latinx",
                "Black, Non-Latinx",
                "Latinx",
                "Asian, Non-Latinx"
            ],
            'Received General Services' => [
                "All Races",
                "White, Non-Latinx",
                "Black, Non-Latinx",
                "Latinx",
                "Asian, Non-Latinx"
            ],
            'Involvement' => [
                'Personal',
                'Familial'
            ],
            'Risk Factors' => [
                'Zero risks',
                'One to two risks',
                'Three or more risks'
            ],
            'Length of Care' => [
                'Full Day, Year Round',
                'School Day, School Year'
            ],
            'Average Credit' => [
                'City EITC',
                'State EITC',
                'City CDCC',
                'State CDCC'
            ],
            'Language' => [
                'English',
                'Spanish',
                'Other',
                'Unknown'
            ],
            'Within 45 Days' => [
                "Total", 
                "White", 
                "Black", 
                "Latinx", 
                "Asian/PI/NA"
                ],
            'After 45 Days' => [
                "Total",
                "White",
                "Black",
                "Latinx",
                "Asian/PI/NA"
            ],
            'Index Crimes' => [
                'Index Crimes',
                'Non-Index Crimes'
            ],
            'All Services' => [
                "Total",
                "White",
                "Black",
                "Latinx",
                "Asian/PI/NA"
            ],
            'Some Services' => [
                "Total",
                "White",
                "Black",
                "Latinx",
                "Asian/PI/NA"
            ],
            'No Services' => [
                "Total",
                "White",
                "Black",
                "Latinx",
                "Asian/PI/NA"
            ]
        ];

        foreach ($breakdowns as $parent => $children) {

            if (is_int($parent)) {

                $parent_breakdown = Breakdown::create([
                    'name' => $children,
                ]);

                continue;
            }

            $parent_breakdown = Breakdown::create([
                'name' => $parent,
            ]);


            foreach ($children as $child) {

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
