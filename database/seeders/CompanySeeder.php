<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'slug' => 'harbor-homes-ltd',
                'name' => 'Harbor Homes Ltd',
                'initials' => 'HH',
                'phone' => '07041596214',
                'specialty' => 'Developer · Off-plan and completed estates',
                'city' => 'Lagos & Ogun',
                'state' => 'Lagos',
                'description' => 'Develops modern gated communities with paved drainage, C of O titles, and flexible milestone payment plans.',
                'credentials' => 'CAC · SCUML · Tax clearance',
                'logo_path' => null,
                'verification_status' => 'verified',
                'is_approved' => true,
            ],
            [
                'slug' => 'crestview-properties',
                'name' => 'Crestview Properties',
                'initials' => 'CP',
                'phone' => '08196224596',
                'specialty' => 'Property company · Residential',
                'city' => 'Abuja',
                'state' => 'FCT',
                'description' => 'Full-service residential brokerage and estate development company serving Maitama, Asokoro, and Katampe.',
                'credentials' => 'CAC · SCUML · Tax clearance',
                'logo_path' => null,
                'verification_status' => 'verified',
                'is_approved' => true,
            ],
            [
                'slug' => 'greenridge-developers',
                'name' => 'Greenridge Developers',
                'initials' => 'GR',
                'phone' => '08045885169',
                'specialty' => 'Developer · Gated Estates & Plots',
                'city' => 'Ibeju-Lekki',
                'state' => 'Lagos',
                'description' => 'Delivers surveyed residential land parcels and smart detached homes along the Lekki-Epe growth corridor.',
                'credentials' => 'CAC · SCUML · REDAN member',
                'logo_path' => null,
                'verification_status' => 'verified',
                'is_approved' => true,
            ],
            [
                'slug' => 'riverline-estates',
                'name' => 'Riverline Estates',
                'initials' => 'RE',
                'phone' => '08077447851',
                'specialty' => 'Property company · Commercial & Luxury',
                'city' => 'Port Harcourt',
                'state' => 'Rivers',
                'description' => 'Commercial office plazas, luxury duplexes, and corporate facility leases across Port Harcourt.',
                'credentials' => 'CAC · SCUML · Tax clearance',
                'logo_path' => null,
                'verification_status' => 'verified',
                'is_approved' => true,
            ],
            [
                'slug' => 'crownstone-buildings',
                'name' => 'Crownstone Buildings',
                'initials' => 'CB',
                'phone' => '08156998541',
                'specialty' => 'Developer · Urban Apartments',
                'city' => 'Ibadan',
                'state' => 'Oyo',
                'description' => 'Constructs contemporary multi-unit apartment residences and commercial retail centers in Oyo State.',
                'credentials' => 'CAC · REDAN member',
                'logo_path' => null,
                'verification_status' => 'verified',
                'is_approved' => true,
            ],
        ];

        foreach ($companies as $company) {
            $user = User::create([
                "name"      => $company['name'],
                "email"     => Str::slug($company['name']).'@gmail.com',
                "password" => Hash::make("password"),
                "role"      => 'company',
                "phone"     => $company['phone'],
                "email_verified_at"    => "2026-11-14 23:31:45",
                "status"        => 'active'
            ]);
            $company['user_id'] = $user->id;
            $company['email'] = $user->email;
            $company['phone'] = $user->phone;
            Company::updateOrCreate(['slug' => $company['slug']], $company);
        }
    }
}
