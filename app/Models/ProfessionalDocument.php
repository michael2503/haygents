<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalDocument extends Model
{
    protected $fillable = [
        'user_id',
        'professional_profile_id',
        'entity_type',
        'document_key',
        'document_label',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'verification_status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function professionalProfile(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProfile::class);
    }
}
