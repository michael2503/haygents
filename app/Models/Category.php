<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Category extends Model
{
    protected $fillable = ['slug', 'name', 'description', 'icon_class', 'sort_order'];

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}

