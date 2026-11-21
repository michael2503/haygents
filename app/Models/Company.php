<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Company extends Model
{
    protected $fillable = [
        'slug', 'user_id', 'name', 'initials', 'specialty', 'city', 'state', 'email', 'phone',
        'description', 'credentials', 'logo_path', 'verification_status', 'is_approved',
    ];

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

}

