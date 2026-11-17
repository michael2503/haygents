<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Professional extends Model
{
    protected $fillable = [
        'slug', 'name', 'initials', 'category', 'category_slug', 'years_exp',
        'city', 'state', 'description', 'credentials', 'avatar_path', 'verification_status', 'is_approved',
    ];
}
