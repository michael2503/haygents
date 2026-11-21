<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Illuminate\Database\Eloquent\Relations\HasMany;

// class Category extends Model
// {
//     protected $fillable = ['slug', 'name', 'description', 'icon_class', 'sort_order'];

//     public function properties(): HasMany
//     {
//         return $this->hasMany(Property::class);
//     }
// }

// class City extends Model
// {
//     protected $fillable = ['slug', 'name', 'state', 'neighborhoods', 'image_path', 'sort_order'];

//     public function properties(): HasMany
//     {
//         return $this->hasMany(Property::class, 'city_id');
//     }
// }

// class Agent extends Model
// {
//     protected $fillable = [
//         'user_id', 'slug', 'name', 'initials', 'role_title', 'years_exp', 'city', 'state',
//         'description', 'credentials', 'verification_status', 'phone', 'email', 'avatar_path', 'is_approved',
//     ];

//     public function user(): BelongsTo
//     {
//         return $this->belongsTo(User::class);
//     }

//     public function properties(): HasMany
//     {
//         return $this->hasMany(Property::class);
//     }
// }

// class Company extends Model
// {
//     protected $fillable = [
//         'user_id', 'slug', 'name', 'initials', 'specialty', 'city', 'state',
//         'description', 'credentials', 'logo_path', 'verification_status', 'is_approved',
//     ];

//     public function user(): BelongsTo
//     {
//         return $this->belongsTo(User::class);
//     }

//     public function properties(): HasMany
//     {
//         return $this->hasMany(Property::class);
//     }
// }

// class Professional extends Model
// {
//     protected $fillable = [
//         'user_id', 'slug', 'name', 'initials', 'category', 'category_slug', 'years_exp',
//         'city', 'state', 'description', 'credentials', 'avatar_path', 'verification_status', 'is_approved',
//     ];

//     public function user(): BelongsTo
//     {
//         return $this->belongsTo(User::class);
//     }
// }

// class Testimonial extends Model
// {
//     protected $fillable = ['customer_name', 'customer_role', 'location', 'rating', 'quote', 'avatar_path', 'is_published'];
// }

// class Faq extends Model
// {
//     protected $fillable = ['question', 'answer', 'sort_order', 'is_published'];
// }

// class Favorite extends Model
// {
//     protected $fillable = ['user_id', 'property_id'];
// }

// class InspectionRequest extends Model
// {
//     protected $fillable = ['property_id', 'user_id', 'full_name', 'email', 'phone', 'preferred_date', 'notes', 'status'];
// }

// class SupportInquiry extends Model
// {
//     protected $fillable = ['full_name', 'email', 'phone', 'subject', 'property_ref', 'message', 'status'];
// }
