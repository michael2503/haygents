<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PropertySearchService
{
    public function search(array $filters): LengthAwarePaginator
    {
        $query = Property::query()->with(['category', 'agent', 'company']);

        if (!empty($filters['location'])) {
            $term = '%' . trim($filters['location']) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('address_area', 'like', $term)
                  ->orWhere('city', 'like', $term)
                  ->orWhere('state', 'like', $term);
            });
        }

        if (!empty($filters['area'])) {
            $areaTerm = '%' . trim($filters['area']) . '%';
            $query->where(function ($q) use ($areaTerm) {
                $q->where('address_area', 'like', $areaTerm)
                  ->orWhere('city', 'like', $areaTerm);
            });
        }

        if (!empty($filters['city']) && $filters['city'] !== 'all') {
            $query->where('city', $filters['city']);
        }

        if (!empty($filters['state']) && $filters['state'] !== 'all') {
            $query->where('state', $filters['state']);
        }

        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $query->whereHas('category', fn ($q) => $q->where('slug', $filters['category']));
        }

        if (!empty($filters['purpose']) && $filters['purpose'] !== 'all') {
            $query->where('purpose', $filters['purpose']);
        }

        if (!empty($filters['bedrooms']) && $filters['bedrooms'] !== 'any') {
            $query->where('bedrooms', '=', (int) $filters['bedrooms']);
        }

        if (!empty($filters['bathrooms']) && $filters['bathrooms'] !== 'any') {
            $query->where('bathrooms', '=', (int) $filters['bathrooms']);
        }

        if (!empty($filters['furnishing']) && $filters['furnishing'] !== 'any') {
            $query->where('furnishing', $filters['furnishing']);
        }

        if (!empty($filters['checkPhysical'])) {
            $query->where('inspection_status', 'verified');
        }

        if (!empty($filters['checkOwnership'])) {
            $query->where('ownership_doc_status', 'verified');
        }

        if (!empty($filters['checkSurvey'])) {
            $query->where('title_doc_status', 'verified');
        }

        if (!empty($filters['keywords'])) {
            $kw = '%' . trim($filters['keywords']) . '%';
            $query->where(function ($q) use ($kw) {
                $q->where('title', 'like', $kw)
                  ->orWhere('description', 'like', $kw)
                  ->orWhere('features', 'like', $kw);
            });
        }

        if (!empty($filters['features']) && is_array($filters['features'])) {
            foreach ($filters['features'] as $feat) {
                $f = '%' . trim((string) $feat) . '%';
                $query->where(function ($q) use ($f) {
                    $q->where('features', 'like', $f)->orWhere('description', 'like', $f);
                });
            }
        }

        if (!empty($filters['ref']) && preg_match('/(\d+)$/', $filters['ref'], $m)) {
            $idNum = (int) $m[1];
            $targetId = $idNum >= 20400 ? $idNum - 20400 : $idNum;
            $query->where('id', $targetId);
        }

        if (!empty($filters['minPrice'])) {
            $query->where('price', '>=', (int) $filters['minPrice']);
        }

        if (!empty($filters['maxPrice'])) {
            $query->where('price', '<=', (int) $filters['maxPrice']);
        }

        $range = $filters['priceRange'] ?? ($filters['price_range'] ?? '');
        if (!empty($range) && $range !== 'any') {
            match ($range) {
                '0-5000000' => $query->where('price', '<=', 5000000),
                '5000000-25000000' => $query->whereBetween('price', [5000000, 25000000]),
                '25000000-75000000' => $query->whereBetween('price', [25000000, 75000000]),
                '75000000-150000000' => $query->whereBetween('price', [75000000, 150000000]),
                '150000000+' => $query->where('price', '>=', 150000000),
                default => null,
            };
        }

        $sort = $filters['sort'] ?? 'newest';
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->orderByDesc('is_featured')->latest();
        }

        return $query->paginate(6)->withQueryString();
    }
}
