<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class InspectionRequest extends Model
{
    protected $fillable = ['property_id', 'user_id', 'full_name', 'email', 'phone', 'preferred_date', 'notes', 'status'];
}
