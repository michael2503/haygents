<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Faq extends Model
{
    protected $fillable = ['question', 'answer', 'sort_order', 'is_published'];
}

