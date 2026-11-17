<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertySearchRequest;
use App\Models\Favorite;
use App\Models\Property;
use App\Services\PropertySearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(PropertySearchRequest $request, PropertySearchService $searchService): View
    {
        $filters = $request->validated();
        $properties = $searchService->search($filters);
        $favoriteIds = Auth::check()
            ? Favorite::where('user_id', Auth::id())->pluck('property_id')->toArray()
            : [];

        return view('properties.index', compact('properties', 'filters', 'favoriteIds'));
    }

    public function show(Property $property): View
    {
        $property->load(['category', 'agent', 'company']);
        $related = Property::where('id', '!=', $property->id)
            ->where(fn ($q) => $q->where('city', $property->city)->orWhere('category_id', $property->category_id))
            ->limit(4)
            ->get();

        $favoriteIds = Auth::check()
            ? Favorite::where('user_id', Auth::id())->pluck('property_id')->toArray()
            : [];

        return view('properties.show', compact('property', 'related', 'favoriteIds'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'purpose' => 'required|in:sale,rent,lease,shortlet',
            'price' => 'required|integer|min:1000',
            'address_area' => 'required|string|max:255',
            'city' => 'required|string|max:120',
            'state' => 'required|string|max:120',
            'description' => 'required|string',
            'image' => 'nullable|image|max:4096',
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('properties', 'public')
            : 'assets/images/prop_lekki_duplex.jpg';

        $property = Property::create([
            ...$validated,
            'slug' => Str::slug($validated['title']) . '-' . Str::random(5),
            'image_path' => $imagePath,
            'verification_status' => 'pending',
        ]);

        return redirect()->route('properties.show', $property->slug)
            ->with('status', 'Property submitted and queued for verification.');
    }
}
