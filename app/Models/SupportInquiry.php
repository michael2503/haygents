<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class SupportInquiry extends Model
{
    protected $fillable = ['full_name', 'email', 'phone', 'subject', 'property_ref', 'message', 'status'];
}
