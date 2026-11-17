<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactInquiryRequest;
use App\Models\Agent;
use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use App\Models\Faq;
use App\Models\Favorite;
use App\Models\Professional;
use App\Models\Property;
use App\Models\SupportInquiry;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // $arr = [
        //     'assets/images/property/house1.png',
        //     'assets/images/property/house2.png',
        //     'assets/images/property/house3.png',
        //     'assets/images/property/house4.png',
        //     'assets/images/property/house5.png',
        //     'assets/images/property/house6.png',
        // ];
        // return json_encode($arr);
        $categories = Category::orderBy('sort_order')->get();

        $featuredProperties = Property::with(['category', 'agent'])
            ->where('is_featured', true)
            ->orderBy('id')
            ->limit(8)
            ->get();

        $cities = City::withCount('properties')
            ->orderBy('sort_order')
            ->get();

        $agents = Agent::withCount('properties')
            ->where('is_approved', true)
            ->limit(4)
            ->get();

        $companies = Company::withCount('properties')
            ->where('is_approved', true)
            ->limit(10)
            ->get();

        $professionals = Professional::where('is_approved', true)
            ->limit(4)
            ->get();

        $testimonials = Testimonial::where('is_published', true)->get();
        $faqs = Faq::where('is_published', true)->orderBy('sort_order')->get();

        $favoriteIds = Auth::check()
            ? Favorite::where('user_id', Auth::id())->pluck('property_id')->toArray()
            : [];

        return view('home.index', compact(
            'categories',
            'featuredProperties',
            'cities',
            'agents',
            'companies',
            'professionals',
            'testimonials',
            'faqs',
            'favoriteIds'
        ));
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function contact(Request $request): View
    {
        $prefillTopic = (string) $request->query('topic', 'A property listing');
        $prefillRef = (string) $request->query('ref', '');
        return view('pages.contact', compact('prefillTopic', 'prefillRef'));
    }

    public function submitContact(StoreContactInquiryRequest $request): RedirectResponse
    {
        SupportInquiry::create([
            ...$request->validated(),
            'status' => 'open',
        ]);

        return redirect()
            ->route('pages.contact')
            ->with('status', 'Your message has been sent. Our HAYGENTS team will reply within one working day.');
    }

    public function sitemap(): Response
    {
        $properties = Property::select('slug', 'updated_at')->get();
        return response()
            ->view('sitemap', compact('properties'))
            ->header('Content-Type', 'application/xml');
    }
}
