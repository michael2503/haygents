<?php

namespace Database\Seeders;

use App\Models\Professional;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ProfessionalSeeder extends Seeder
{
    public function run(): void
    {
        $professionals = [
            [
                'slug' => 'chidinma-nwosu',
                'name' => 'Chidinma Nwosu',
                'phone' => '08096114751',
                'initials' => 'CN',
                'category' => 'Lawyer',
                'category_slug' => 'lawyers',
                'years_exp' => 12,
                'city' => 'Ikeja',
                'state' => 'Lagos',
                'description' => 'Conducts Alausa Land Registry title searches, drafts Deeds of Assignment, and oversees Governor’s Consent.',
                'credentials' => 'Call to Bar · NBA seal verified',
                'avatar_path' => null,
                'verification_status' => 'verified',
                'is_approved' => true,
            ],
            [
                'slug' => 'kunle-ogunleye',
                'name' => 'Kunle Ogunleye',
                'phone' => '07085119621',
                'initials' => 'KO',
                'category' => 'Surveyor',
                'category_slug' => 'surveyors',
                'years_exp' => 9,
                'city' => 'Abeokuta & Lagos',
                'state' => 'Ogun',
                'description' => 'Performs boundary charting, beacon verification, and Red Copy survey confirmations at state Surveyor-General offices.',
                'credentials' => 'SURCON registered and sealed',
                'avatar_path' => null,
                'verification_status' => 'verified',
                'is_approved' => true,
            ],
            [
                'slug' => 'bola-akande',
                'name' => 'Bola Akande',
                'phone' => '07096551408',
                'initials' => 'BA',
                'category' => 'Valuer',
                'category_slug' => 'valuers',
                'years_exp' => 15,
                'city' => 'Ikoyi',
                'state' => 'Lagos',
                'description' => 'Delivers bank-grade market valuations, rental yield appraisals, and pre-purchase structural cost assessments.',
                'credentials' => 'ESVARBON registered · NIESV member',
                'avatar_path' => null,
                'verification_status' => 'verified',
                'is_approved' => true,
            ],
            [
                'slug' => 'oluwaseun-adeyemi',
                'name' => 'Oluwaseun Adeyemi',
                'phone' => '08051447851',
                'initials' => 'OA',
                'category' => 'Valuer',
                'category_slug' => 'valuers',
                'years_exp' => 8,
                'city' => 'Wuse 2',
                'state' => 'Abuja',
                'description' => 'Advises diaspora investors and institutions on FCDA title verification, project feasibility, and portfolio acquisition.',
                'credentials' => 'NIESV associate · SCUML verified',
                'avatar_path' => null,
                'verification_status' => 'verified',
                'is_approved' => true,
            ],
            [
                'slug' => 'aisha-danladi',
                'name' => 'Aisha Danladi',
                'phone' => '08163994569',
                'initials' => 'AD',
                'category' => 'Lawyer',
                'category_slug' => 'lawyers',
                'years_exp' => 10,
                'city' => 'Maitama',
                'state' => 'Abuja',
                'description' => 'Specializes in AGIS title verification, commercial lease structuring, and escrow closing documentation.',
                'credentials' => 'Call to Bar · NBA seal verified',
                'avatar_path' => null,
                'verification_status' => 'verified',
                'is_approved' => true,
            ],
            [
                'slug' => 'chinedu-okeke',
                'name' => 'Chinedu Okeke',
                'phone' => '08015669541',
                'initials' => 'CO',
                'category' => 'Surveyor',
                'category_slug' => 'surveyors',
                'years_exp' => 7,
                'city' => 'GRA',
                'state' => 'Rivers',
                'description' => 'Provides GIS coordinate charting, topographic surveys, and site boundary confirmation for residential estates.',
                'credentials' => 'SURCON registered · Pending annual renewal',
                'avatar_path' => null,
                'verification_status' => 'pending',
                'is_approved' => true,
            ],
        ];

        foreach ($professionals as $professional) {
            $user = User::create([
                "name"      => $professional['name'],
                "email"     => Str::slug($professional['name']).'@gmail.com',
                "password" => Hash::make("password"),
                "role"      => $professional['category_slug'],
                "phone"     => $professional['phone'],
                "email_verified_at"    => "2026-11-14 23:31:45",
                "status"        => 'active'
            ]);
            $professional['user_id'] = $user->id;
            $professional['email'] = $user->email;
            $professional['phone'] = $user->phone;
            Professional::updateOrCreate(['slug' => $professional['slug']], $professional);
        }
    }
}



// <?php

// namespace Database\Seeders;

// use App\Models\ProfessionalService;
// use Illuminate\Database\Seeder;

// class ProfessionalServiceSeeder extends Seeder
// {
//     public function run(): void
//     {
//         $services = [
//             // Property Lawyer Services
//             ['profession_type' => 'lawyer', 'slug' => 'lawyer-title-search', 'name' => 'Title search', 'description' => 'Land registry search and root of title verification', 'sort_order' => 1],
//             ['profession_type' => 'lawyer', 'slug' => 'lawyer-deed-of-assignment', 'name' => 'Deed of assignment', 'description' => 'Drafting and execution of Deeds of Assignment', 'sort_order' => 2],
//             ['profession_type' => 'lawyer', 'slug' => 'lawyer-governors-consent', 'name' => "Governor's consent", 'description' => 'Perfection of title and Governor Consent processing', 'sort_order' => 3],
//             ['profession_type' => 'lawyer', 'slug' => 'lawyer-contract-review', 'name' => 'Contract review', 'description' => 'Review of sale, purchase and joint venture agreements', 'sort_order' => 4],
//             ['profession_type' => 'lawyer', 'slug' => 'lawyer-lease-agreements', 'name' => 'Lease agreements', 'description' => 'Residential and commercial tenancy/lease drafting', 'sort_order' => 5],
//             ['profession_type' => 'lawyer', 'slug' => 'lawyer-power-of-attorney', 'name' => 'Power of attorney', 'description' => 'Irrevocable and general property powers of attorney', 'sort_order' => 6],
//             ['profession_type' => 'lawyer', 'slug' => 'lawyer-registration-of-title', 'name' => 'Registration of title', 'description' => 'Certificate of Occupancy and title registration', 'sort_order' => 7],
//             ['profession_type' => 'lawyer', 'slug' => 'lawyer-off-plan-contract-review', 'name' => 'Off-plan contract review', 'description' => 'Developer due diligence and off-plan agreement review', 'sort_order' => 8],

//             // Land Surveyor Services
//             ['profession_type' => 'surveyor', 'slug' => 'surveyor-boundary-survey', 'name' => 'Boundary survey', 'description' => 'Beacon re-establishment and boundary delineation', 'sort_order' => 1],
//             ['profession_type' => 'surveyor', 'slug' => 'surveyor-survey-plan-preparation', 'name' => 'Survey plan preparation', 'description' => 'SURCON-sealed survey plan preparation and charting', 'sort_order' => 2],
//             ['profession_type' => 'surveyor', 'slug' => 'surveyor-site-verification', 'name' => 'Site verification', 'description' => 'Coordinate charting and acquisition status verification', 'sort_order' => 3],
//             ['profession_type' => 'surveyor', 'slug' => 'surveyor-perimeter-survey', 'name' => 'Perimeter survey', 'description' => 'Estate and large parcel perimeter surveying', 'sort_order' => 4],
//             ['profession_type' => 'surveyor', 'slug' => 'surveyor-topographic-survey', 'name' => 'Topographic survey', 'description' => 'Elevation, contour and terrain mapping', 'sort_order' => 5],
//             ['profession_type' => 'surveyor', 'slug' => 'surveyor-setting-out', 'name' => 'Setting out', 'description' => 'Construction grid and foundation setting out', 'sort_order' => 6],
//             ['profession_type' => 'surveyor', 'slug' => 'surveyor-land-subdivision', 'name' => 'Land subdivision', 'description' => 'Layout partitioning into approved plot allocations', 'sort_order' => 7],
//             ['profession_type' => 'surveyor', 'slug' => 'surveyor-as-built-survey', 'name' => 'As-built survey', 'description' => 'Post-construction structure and boundary verification', 'sort_order' => 8],

//             // Estate Surveyor and Valuer Services
//             ['profession_type' => 'valuer', 'slug' => 'valuer-property-valuation', 'name' => 'Property valuation', 'description' => 'ESVARBON-compliant open market property valuation', 'sort_order' => 1],
//             ['profession_type' => 'valuer', 'slug' => 'valuer-market-analysis', 'name' => 'Market analysis', 'description' => 'Comparative sales and neighbourhood yield analysis', 'sort_order' => 2],
//             ['profession_type' => 'valuer', 'slug' => 'valuer-investment-report', 'name' => 'Investment report', 'description' => 'Capital appreciation and cashflow appraisal report', 'sort_order' => 3],
//             ['profession_type' => 'valuer', 'slug' => 'valuer-rent-review', 'name' => 'Rent review', 'description' => 'Fair market rental assessment and renewal advisory', 'sort_order' => 4],
//             ['profession_type' => 'valuer', 'slug' => 'valuer-property-inspection', 'name' => 'Property inspection', 'description' => 'Physical condition and asset audit inspection', 'sort_order' => 5],
//             ['profession_type' => 'valuer', 'slug' => 'valuer-feasibility-study', 'name' => 'Feasibility study', 'description' => 'Development viability and cost-return study', 'sort_order' => 6],
//             ['profession_type' => 'valuer', 'slug' => 'valuer-insurance-valuation', 'name' => 'Insurance valuation', 'description' => 'Reinstatement and replacement cost assessment', 'sort_order' => 7],
//             ['profession_type' => 'valuer', 'slug' => 'valuer-mortgage-valuation', 'name' => 'Mortgage valuation', 'description' => 'Bank and mortgage collateral security valuation', 'sort_order' => 8],
//         ];

//         foreach ($services as $service) {
//             ProfessionalService::updateOrCreate(
//                 ['slug' => $service['slug']],
//                 [
//                     'profession_type' => $service['profession_type'],
//                     'name' => $service['name'],
//                     'description' => $service['description'],
//                     'is_active' => true,
//                     'sort_order' => $service['sort_order'],
//                 ]
//             );
//         }
//     }
// }
