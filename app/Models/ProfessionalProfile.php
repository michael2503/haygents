<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfessionalProfile extends Model
{
    protected $fillable = [
        'user_id',
        'professional_id',
        'profession_type',
        'practice_mode',
        'year_called_to_bar',
        'nba_branch',
        'supreme_court_enrolment_number',
        'nba_seal_number',
        'surcon_registration_number',
        'year_registered',
        'esvarbon_registration_number',
        'niesv_membership',
        'practising_licence_number',
        'firm_name',
        'firm_cac_rc_number',
        'states_covered',
        'service_modes',
        'languages',
        'typical_turnaround',
        'display_name',
        'years_exp',
        'main_area',
        'bio',
        'profile_photo_path',
        'completed_sub_steps',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'states_covered' => 'array',
            'service_modes' => 'array',
            'languages' => 'array',
            'completed_sub_steps' => 'array',
            'submitted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(
            ProfessionalService::class,
            'professional_profile_service',
            'professional_profile_id',
            'professional_service_id'
        )->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProfessionalDocument::class);
    }
}
