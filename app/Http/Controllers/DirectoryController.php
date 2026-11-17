<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInspectionRequest;
use App\Models\Agent;
use App\Models\Company;
use App\Models\Favorite;
use App\Models\InspectionRequest;
use App\Models\Professional;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DirectoryController extends Controller
{
    public function agents(): View
    {
        $agents = Agent::withCount('properties')->where('is_approved', true)->paginate(12);
        return view('agents.index', compact('agents'));
    }

    public function companies(): View
    {
        $companies = Company::withCount('properties')->where('is_approved', true)->paginate(12);
        return view('companies.index', compact('companies'));
    }

    public function professionals(Request $request): View
    {
        $category = $request->query('category', 'all');
        $query = Professional::where('is_approved', true);
        if ($category !== 'all') {
            $query->where('category_slug', $category);
        }
        $professionals = $query->paginate(12)->withQueryString();
        return view('professionals.index', compact('professionals', 'category'));
    }
}

class FavoriteController extends Controller
{
    public function toggle(Request $request): JsonResponse
    {
        $request->validate(['property_id' => 'required|exists:properties,id']);
        $userId = Auth::id();
        $propertyId = (int) $request->input('property_id');

        $existing = Favorite::where('user_id', $userId)->where('property_id', $propertyId)->first();
        if ($existing) {
            $existing->delete();
            return response()->json(['favorited' => false]);
        }

        Favorite::create(['user_id' => $userId, 'property_id' => $propertyId]);
        return response()->json(['favorited' => true]);
    }
}

class InspectionController extends Controller
{
    public function store(StoreInspectionRequest $request): RedirectResponse
    {
        InspectionRequest::create([
            ...$request->validated(),
            'user_id' => Auth::id(),
            'status' => 'pending',
        ]);

        return back()->with('status', 'Inspection request submitted successfully.');
    }
}
