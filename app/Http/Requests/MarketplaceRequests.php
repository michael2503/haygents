<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertySearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'location' => 'nullable|string|max:120',
            'area' => 'nullable|string|max:120',
            'category' => 'nullable|string|max:60',
            'purpose' => 'nullable|in:all,sale,rent,lease,shortlet',
            'priceRange' => 'nullable|string|max:40',
            'price_range' => 'nullable|string|max:40',
            'minPrice' => 'nullable|numeric|min:0',
            'maxPrice' => 'nullable|numeric|min:0',
            'bedrooms' => 'nullable|string|max:10',
            'bathrooms' => 'nullable|string|max:10',
            'furnishing' => 'nullable|string|max:40',
            'features' => 'nullable|array',
            'features.*' => 'string|max:80',
            'checkPhysical' => 'nullable|boolean',
            'checkOwnership' => 'nullable|boolean',
            'checkSurvey' => 'nullable|boolean',
            'keywords' => 'nullable|string|max:120',
            'ref' => 'nullable|string|max:40',
            'city' => 'nullable|string|max:80',
            'state' => 'nullable|string|max:80',
            'sort' => 'nullable|in:newest,price_asc,price_desc',
        ];
    }
}

class StoreInspectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'full_name' => 'required|string|max:120',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'preferred_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}

class StoreContactInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:120',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:30',
            'subject' => 'required|string|max:180',
            'property_ref' => 'nullable|string|max:40',
            'message' => 'required|string|max:2500',
        ];
    }
}
