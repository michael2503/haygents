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
    public function agents(Request $request): View
    {
        $filters = $request->only(['agentType', 'state', 'city', 'area', 'years', 'specialisation']);
        $query = Agent::withCount('properties')->where('is_approved', true);

        if (!empty($filters['agentType']) && $filters['agentType'] !== 'all') {
            $type = $filters['agentType'];
            $query->where(function ($q) use ($type) {
                if ($type === 'estate-agent') {
                    $q->where('role_title', 'like', '%Agent%');
                } elseif ($type === 'realtor') {
                    $q->where('role_title', 'like', '%Realtor%');
                } else {
                    $q->where('role_title', 'like', '%Consultant%')
                      ->orWhere('role_title', 'like', '%Broker%')
                      ->orWhere('role_title', 'like', '%Manager%');
                }
            });
        }

        if (!empty($filters['state']) && $filters['state'] !== 'all') {
            $st = $filters['state'];
            if (in_array(strtolower($st), ['fct', 'abuja', 'abuja (fct)'])) {
                $query->where(fn ($q) => $q->where('state', 'like', '%Abuja%')->orWhere('state', 'like', '%FCT%'));
            } else {
                $query->where('state', 'like', '%' . $st . '%');
            }
        }

        if (!empty($filters['city']) && $filters['city'] !== 'all') {
            $query->where('city', 'like', '%' . $filters['city'] . '%');
        }

        if (!empty($filters['area'])) {
            $area = '%' . trim($filters['area']) . '%';
            $query->where(fn ($q) => $q->where('city', 'like', $area)->orWhere('description', 'like', $area));
        }

        if (!empty($filters['years']) && $filters['years'] !== 'any') {
            $query->where('years_exp', '>=', (int) $filters['years']);
        }

        if (!empty($filters['specialisation']) && $filters['specialisation'] !== 'all') {
            $specs = is_array($filters['specialisation'])
                ? $filters['specialisation']
                : explode(',', (string) $filters['specialisation']);

            foreach ($specs as $sp) {
                $sp = strtolower(trim((string) $sp));
                if (!$sp) continue;
                $query->where(function ($q) use ($sp) {
                    if ($sp === 'rentals') {
                        $q->where('description', 'like', '%lease%')
                          ->orWhere('description', 'like', '%tenant%')
                          ->orWhere('description', 'like', '%serviced%');
                    } elseif ($sp === 'sales') {
                        $q->where('description', 'like', '%duplex%')
                          ->orWhere('description', 'like', '%home%')
                          ->orWhere('description', 'like', '%buyer%');
                    } else {
                        $q->where('description', 'like', '%' . $sp . '%')
                          ->orWhere('role_title', 'like', '%' . $sp . '%');
                    }
                });
            }
        }

        $agents = $query->orderBy('id')->paginate(6)->withQueryString();
        return view('agents.index', compact('agents', 'filters'));
    }

    public function companies(): View
    {
        $companies = Company::withCount('properties')->where('is_approved', true)->paginate(12);
        return view('companies.index', compact('companies'));
    }

    public function professionals(Request $request): View
    {
        $filters = $request->only(['category', 'state', 'city', 'area', 'years', 'service']);
        $category = $filters['category'] ?? 'all';

        $query = Professional::where('is_approved', true);

        if (!empty($category) && $category !== 'all') {
            if ($category === 'independent-valuers') {
                $query->whereIn('category_slug', ['property-valuers', 'real-estate-consultants']);
            } else {
                $query->where('category_slug', $category);
            }
        }

        if (!empty($filters['state']) && $filters['state'] !== 'all') {
            $st = $filters['state'];
            if (in_array(strtolower($st), ['fct', 'abuja', 'abuja (fct)'])) {
                $query->where(fn ($q) => $q->where('state', 'like', '%Abuja%')->orWhere('state', 'like', '%FCT%'));
            } else {
                $query->where('state', 'like', '%' . $st . '%');
            }
        }

        if (!empty($filters['city']) && $filters['city'] !== 'all') {
            $query->where('city', 'like', '%' . $filters['city'] . '%');
        }

        if (!empty($filters['area'])) {
            $area = '%' . trim($filters['area']) . '%';
            $query->where(fn ($q) => $q->where('city', 'like', $area)->orWhere('description', 'like', $area));
        }

        if (!empty($filters['years']) && $filters['years'] !== 'any') {
            $query->where('years_exp', '>=', (int) $filters['years']);
        }

        if (!empty($filters['service'])) {
            $services = is_array($filters['service'])
                ? $filters['service']
                : explode(',', (string) $filters['service']);

            foreach ($services as $svc) {
                $svc = trim((string) $svc);
                if (!$svc) continue;
                $query->where(fn ($q) => $q->where('description', 'like', '%' . $svc . '%')->orWhere('credentials', 'like', '%' . $svc . '%')->orWhere('category', 'like', '%' . $svc . '%'));
            }
        }

        $professionals = $query->orderBy('id')->paginate(6)->withQueryString();
        return view('professionals.index', compact('professionals', 'category', 'filters'));
    }

    public function showProfessional(Professional $professional): View
    {
        $others = Professional::where('is_approved', true)
            ->where('id', '!=', $professional->id)
            ->orderBy('id')
            ->limit(4)
            ->get();

        return view('professionals.show', compact('professional', 'others'));
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
