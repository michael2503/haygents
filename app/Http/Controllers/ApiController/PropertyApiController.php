<?php

namespace App\Http\Controllers\ApiController;

use App\Classes\Helper;
use App\Classes\Settings;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PropertySearchRequest;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Property;
use App\Services\PropertySearchService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PropertyApiController extends Controller
{
    use ApiResponse;


    public function index(PropertySearchRequest $request, PropertySearchService $searchService)
    {
        $filters = $request->validated();
        $properties = $searchService->search($filters);
        $favoriteIds = Auth::check()
            ? Favorite::where('user_id', Auth::id())->pluck('property_id')->toArray()
            : [];

        $resp = [
            'filters' => $filters,
            'properties' => $properties,
            'favoriteIds' => $favoriteIds,
        ];
        return $this->successResponse($resp, Response::HTTP_CREATED);

        // purpose: any, rent, sale, lease, shortlet.
        // location: any location
        // category: all, houses, flats-and-apartment, land, commercial, office-spaces, shortlets
        // priceRange: any, 0-5000000, (any range but in this format)
        // bedrooms: any, 1, 2, 3, 4, 5, 6, 7,
    }

    public function show(Property $property)
    {
        $property->load(['category', 'agent', 'company']);
        $related = Property::where('id', '!=', $property->id)
            ->where(fn ($q) => $q->where('city', $property->city)->orWhere('category_id', $property->category_id))
            ->limit(4)
            ->get();

        $favoriteIds = Auth::check()
            ? Favorite::where('user_id', Auth::id())->pluck('property_id')->toArray()
            : [];

        $resp = [
            'related' => $related,
            'property' => $property,
            'favoriteIds' => $favoriteIds,
        ];
        return $this->successResponse($resp, Response::HTTP_CREATED);
    }


}
