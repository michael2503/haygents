<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Agent extends Model
{
    protected $fillable = [
        'slug', 'name', 'initials', 'role_title', 'years_exp', 'city', 'state',
        'description', 'credentials', 'verification_status', 'phone', 'email', 'avatar_path', 'is_approved',
    ];

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
