<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserKycVerification extends Model
{
    protected $fillable = [
        'user_id',
        'legal_full_name',
        'date_of_birth',
        'id_type',
        'id_number',
        'id_front_path',
        'id_back_path',
        'proof_of_address_type',
        'bill_date',
        'proof_of_address_path',
        'passport_photo_path',
        'passport_capture_source',
        'identity_status',
        'legal_details_completed_at',
        'government_id_completed_at',
        'proof_of_address_completed_at',
        'passport_photo_completed_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'bill_date' => 'date',
            'legal_details_completed_at' => 'datetime',
            'government_id_completed_at' => 'datetime',
            'proof_of_address_completed_at' => 'datetime',
            'passport_photo_completed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
