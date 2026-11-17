<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertySearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $normalized = [];

        foreach (['checkPhysical', 'checkOwnership', 'checkSurvey', 'verifiedOnly'] as $boolKey) {
            if ($this->has($boolKey)) {
                $val = filter_var($this->input($boolKey), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($val !== null) {
                    $normalized[$boolKey] = $val;
                }
            }
        }

        if ($this->has('features') && is_string($this->input('features'))) {
            $items = array_values(array_filter(array_map('trim', explode(',', $this->input('features')))));
            $normalized['features'] = $items;
        }

        if (!empty($normalized)) {
            $this->merge($normalized);
        }
    }

    public function rules(): array
    {
        $maxPriceRules = ['nullable', 'numeric', 'min:0'];
        if ($this->filled('minPrice')) {
            $maxPriceRules[] = 'gte:minPrice';
        }

        return [
            'location' => ['nullable', 'string', 'max:120'],
            'area' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:60'],
            'purpose' => ['nullable', 'in:all,sale,rent,lease,shortlet'],
            'priceRange' => ['nullable', 'string', 'max:40'],
            'price_range' => ['nullable', 'string', 'max:40'],
            'minPrice' => ['nullable', 'numeric', 'min:0'],
            'maxPrice' => $maxPriceRules,
            'bedrooms' => ['nullable', 'string', 'max:10'],
            'bathrooms' => ['nullable', 'string', 'max:10'],
            'furnishing' => ['nullable', 'in:any,Furnished,Semi-Furnished,Unfurnished'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:80'],
            'checkPhysical' => ['nullable', 'boolean'],
            'checkOwnership' => ['nullable', 'boolean'],
            'checkSurvey' => ['nullable', 'boolean'],
            'verifiedOnly' => ['nullable', 'boolean'],
            'keywords' => ['nullable', 'string', 'max:120'],
            'ref' => ['nullable', 'string', 'max:40'],
            'city' => ['nullable', 'string', 'max:80'],
            'state' => ['nullable', 'string', 'max:80'],
            'sort' => ['nullable', 'in:newest,price_asc,price_desc'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'purpose.in' => 'The selected property purpose is invalid.',
            'maxPrice.gte' => 'Maximum price must be greater than or equal to the minimum price.',
            'sort.in' => 'The selected sort option is invalid.',
        ];
    }
}
