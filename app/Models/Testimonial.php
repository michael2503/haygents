<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Testimonial extends Model
{
    protected $fillable = ['customer_name', 'customer_role', 'location', 'rating', 'quote', 'avatar_path', 'is_published'];
}
