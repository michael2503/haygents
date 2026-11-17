<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use App\Models\Faq;
use App\Models\Professional;
use App\Models\Property;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class HaygentsDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['slug' => 'houses', 'name' => 'Houses', 'description' => 'Duplexes, bungalows and terraces', 'icon_class' => 'fas fa-home', 'sort_order' => 1],
            ['slug' => 'land-plots', 'name' => 'Land & Plots', 'description' => 'Residential and commercial plots', 'icon_class' => 'fas fa-vector-square', 'sort_order' => 2],
            ['slug' => 'apartments', 'name' => 'Apartments', 'description' => 'From mini flats to penthouses', 'icon_class' => 'fas fa-building', 'sort_order' => 3],
            ['slug' => 'commercial', 'name' => 'Commercial Properties', 'description' => 'Shops, warehouses and plazas', 'icon_class' => 'fas fa-store', 'sort_order' => 4],
            ['slug' => 'shortlets', 'name' => 'Shortlet Apartments', 'description' => 'Furnished stays by the night', 'icon_class' => 'fas fa-key', 'sort_order' => 5],
            ['slug' => 'office-spaces', 'name' => 'Office Spaces', 'description' => 'Floors and whole buildings', 'icon_class' => 'fas fa-briefcase', 'sort_order' => 6],
        ];
        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        $cities = [
            ['slug' => 'lagos', 'name' => 'Lagos', 'state' => 'Lagos', 'neighborhoods' => 'Lekki, Ikoyi, Yaba and more', 'image_path' => 'assets/images/city_lagos.jpg', 'sort_order' => 1],
            ['slug' => 'abuja', 'name' => 'Abuja', 'state' => 'FCT', 'neighborhoods' => 'Maitama, Wuse 2, Gwarinpa and more', 'image_path' => 'assets/images/city_abuja.jpg', 'sort_order' => 2],
            ['slug' => 'port-harcourt', 'name' => 'Port Harcourt', 'state' => 'Rivers', 'neighborhoods' => 'GRA, Trans-Amadi and more', 'image_path' => 'assets/images/city_ph.jpg', 'sort_order' => 3],
            ['slug' => 'ibadan', 'name' => 'Ibadan', 'state' => 'Oyo', 'neighborhoods' => 'Bodija, Jericho and more', 'image_path' => 'assets/images/city_ibadan.jpg', 'sort_order' => 4],
        ];
        foreach ($cities as $c) {
            City::firstOrCreate(['slug' => $c['slug']], $c);
        }

        $faqs = [
            ['question' => 'What is Haygents?', 'answer' => 'Haygents is a real estate marketplace connecting buyers, tenants, landlords, verified agents, developers, property lawyers, surveyors, and valuers.', 'sort_order' => 1],
            ['question' => 'How can I find properties on Haygents?', 'answer' => 'Use the homepage search bar to filter by location, property type, purpose, price range, and bedrooms.', 'sort_order' => 2],
            ['question' => 'How do I list my property?', 'answer' => 'Click List Your Property, sign in, and upload property details and ownership documents for review.', 'sort_order' => 3],
            ['question' => 'Are properties on Haygents verified?', 'answer' => 'Each listing clearly shows Verified, Pending Review, or Unverified status per document and inspection check.', 'sort_order' => 4],
            ['question' => 'Can I request a property inspection?', 'answer' => 'Yes. Open any listing and submit an inspection request for physical or virtual viewing.', 'sort_order' => 5],
            ['question' => 'How do I contact a real estate agent?', 'answer' => 'Click View Profile on any approved agent card to view credentials and request a consultation.', 'sort_order' => 6],
            ['question' => 'Can I find property lawyers and surveyors?', 'answer' => 'Yes. Browse our Property Professionals directory for NBA lawyers, SURCON surveyors, and ESVARBON valuers.', 'sort_order' => 7],
            ['question' => 'How do I register as a property professional?', 'answer' => 'Sign up and submit your professional license credentials (LASRERA, CAC, NBA, SURCON, or ESVARBON) for verification.', 'sort_order' => 8],
        ];
        foreach ($faqs as $f) {
            Faq::firstOrCreate(['question' => $f['question']], $f);
        }

        Testimonial::firstOrCreate(['customer_name' => 'Adanze O.'], [
            'customer_role' => 'Renter', 'location' => 'Lagos', 'rating' => 5,
            'quote' => 'I never had to meet a stranger or pay anyone blindly at the property. The Haygents team coordinated everything until I got my keys.',
        ]);
    }
}
