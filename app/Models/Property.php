<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    protected $fillable = [
        'slug', 'title', 'category_id', 'city_id', 'agent_id', 'company_id',
        'purpose', 'price', 'price_period', 'bedrooms', 'bathrooms', 'parking_spaces', 'size_sqm',
        'address_area', 'city', 'state', 'description', 'features', 'furnishing',
        'image_path', 'is_featured', 'verification_status', 'ownership_doc_status',
        'location_check_status', 'title_doc_status', 'inspection_status', 'legal_doc_status',
    ];

    protected $casts = [
        'features' => 'array',
        'is_featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function cityRecord(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(InspectionRequest::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return '₦' . number_format($this->price);
    }

    public function getRefCodeAttribute(): string
    {
        $c = strtolower((string) $this->city);
        $code = str_contains($c, 'lagos') ? 'LK'
            : (str_contains($c, 'abuja') ? 'AB'
            : (str_contains($c, 'port') ? 'PH'
            : (str_contains($c, 'ibadan') ? 'IB' : 'NG')));
        return 'HGT-' . $code . '-' . (20400 + (int) $this->id);
    }
}
