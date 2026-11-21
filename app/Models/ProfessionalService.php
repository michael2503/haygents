<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProfessionalService extends Model
{
    protected $fillable = [
        'profession_type',
        'slug',
        'name',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function profiles(): BelongsToMany
    {
        return $this->belongsToMany(
            ProfessionalProfile::class,
            'professional_profile_service',
            'professional_service_id',
            'professional_profile_id'
        )->withTimestamps();
    }
}
