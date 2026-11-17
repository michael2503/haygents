<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class City extends Model
{
    protected $fillable = ['slug', 'name', 'state', 'neighborhoods', 'image_path', 'sort_order'];

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'city_id');
    }
}
