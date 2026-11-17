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
