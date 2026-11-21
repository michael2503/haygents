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
use App\Traits\ApiResponse;
use App\Models\Property;
use App\Services\PropertySearchService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class GeneralController extends Controller
{
    use ApiResponse;


    public function index(PropertySearchService $searchService)
    {
        $catego = Category::get();
        $allCountry = $searchService->getCountry();

        $resp = [
            'property_category' => $catego,
            'allCountry' => $allCountry
        ];
        return $this->successResponse($resp, Response::HTTP_CREATED);
    }


}
