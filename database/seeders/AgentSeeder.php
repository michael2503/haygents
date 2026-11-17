<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AgentSeeder extends Seeder
{
    public function run(): void
    {
        $agents = [
            [
                'slug' => 'tunde-bello',
                'name' => 'Tunde Bello',
                'initials' => 'TB',
                'role_title' => 'Principal Estate Agent · 9 years',
                'years_exp' => 9,
                'city' => 'Lekki',
                'state' => 'Lagos',
                'description' => 'Specializes in verified residential duplexes, waterfront homes, and family estates across Lekki and Victoria Island.',
                'credentials' => 'LASRERA licensed · SCUML registered',
                'verification_status' => 'verified',
                'phone' => '+234 803 412 8890',
                'email' => 'tunde.bello@haygents.ng',
                'avatar_path' => null,
                'is_approved' => true,
            ],
            [
                'slug' => 'ngozi-eze',
                'name' => 'Ngozi Eze',
                'initials' => 'NE',
                'role_title' => 'Senior Realtor · 6 years',
                'years_exp' => 6,
                'city' => 'Ikoyi',
                'state' => 'Lagos',
                'description' => 'Advises corporate tenants, diaspora buyers, and serviced apartment investors in Ikoyi and Victoria Island.',
                'credentials' => 'LASRERA licensed · SCUML registered',
                'verification_status' => 'verified',
                'phone' => '+234 809 221 7741',
                'email' => 'ngozi.eze@haygents.ng',
                'avatar_path' => null,
                'is_approved' => true,
            ],
            [
                'slug' => 'ibrahim-musa',
                'name' => 'Ibrahim Musa',
                'initials' => 'IM',
                'role_title' => 'Estate Agent · 11 years',
                'years_exp' => 11,
                'city' => 'Wuse 2',
                'state' => 'Abuja',
                'description' => 'Trusted commercial and residential advisor covering Maitama, Wuse 2, and Central Business District in Abuja.',
                'credentials' => 'REDAN associate · SCUML registered',
                'verification_status' => 'verified',
                'phone' => '+234 805 619 3320',
                'email' => 'ibrahim.musa@haygents.ng',
                'avatar_path' => null,
                'is_approved' => true,
            ],
            [
                'slug' => 'amaka-obi',
                'name' => 'Amaka Obi',
                'initials' => 'AO',
                'role_title' => 'Realtor · 5 years',
                'years_exp' => 5,
                'city' => 'GRA Phase 2',
                'state' => 'Rivers',
                'description' => 'Helps families and oil-and-gas professionals lease and buy vetted homes across Port Harcourt GRA.',
                'credentials' => 'State licensed · SCUML registered',
                'verification_status' => 'verified',
                'phone' => '+234 812 908 4412',
                'email' => 'amaka.obi@haygents.ng',
                'avatar_path' => null,
                'is_approved' => true,
            ],
            [
                'slug' => 'segun-adesina',
                'name' => 'Segun Adesina',
                'initials' => 'SA',
                'role_title' => 'Estate Consultant · 8 years',
                'years_exp' => 8,
                'city' => 'Bodija',
                'state' => 'Oyo',
                'description' => 'Focused on titled residential plots and newly built bungalows across Bodija, Jericho, and Akobo in Ibadan.',
                'credentials' => 'State licensed · NIESV affiliate',
                'verification_status' => 'verified',
                'phone' => '+234 806 334 1190',
                'email' => 'segun.adesina@haygents.ng',
                'avatar_path' => null,
                'is_approved' => true,
            ],
            [
                'slug' => 'kemi-williams',
                'name' => 'Kemi Williams',
                'initials' => 'KW',
                'role_title' => 'Estate Agent · 4 years',
                'years_exp' => 4,
                'city' => 'Victoria Island',
                'state' => 'Lagos',
                'description' => 'Manages inspected shortlet residences and executive corporate accommodations in Lagos Island.',
                'credentials' => 'LASRERA registered · KYC verified',
                'verification_status' => 'verified',
                'phone' => '+234 814 551 6070',
                'email' => 'kemi.williams@haygents.ng',
                'avatar_path' => null,
                'is_approved' => true,
            ],
        ];

        foreach ($agents as $agent) {
            $user = User::create([
                "name"      => $agent['name'],
                "email"     => $agent['email'],
                "password" => Hash::make("password"),
                "role"      => 'agent',
                "phone"     => $agent['phone'],
                "email_verified_at"    => "2026-11-14 23:31:45",
                "status"        => 'active'
            ]);
            $agent['user_id'] = $user->id;
            Agent::updateOrCreate(['slug' => $agent['slug']], $agent);
        }
    }
}
