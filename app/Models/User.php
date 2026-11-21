<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ACCOUNT_TYPES = [
        'agent',
        'property-owner',
        'customer',
        'company',
        'professional',
        'lawyer',
        'surveyor',
        'valuer',
        'super-admin',
        'staff',
    ];

    public const PUBLIC_REGISTER_TYPES = [
        'agent',
        'property-owner',
        'customer',
        'company',
        'professional',
        'lawyer',
        'surveyor',
        'valuer',
    ];

    public const STAFF_ROLES = [
        'valuer',
        'lawyer',
        'marketer',
        'surveyor',
    ];

    public const PARTNER_ACCOUNT_TYPES = [
        'agent',
        'company',
        'professional',
        'lawyer',
        'surveyor',
        'valuer',
    ];

    public const PROFESSIONAL_ACCOUNT_TYPES = [
        'professional',
        'lawyer',
        'surveyor',
        'valuer',
    ];


    protected $fillable = [
        "name",
        "email",
        "password",
        "api_token",
        "account_type",
        "staff_role",
        "dial_code",
        "phone",
        "country",
        "state",

        'lga',
        'referral_code',
        'referred_by_id',
        'account_officer_id',
        'referral_type',
        'terms_accepted_at',
        'email_verified_at',
        'phone_verified_at',
        'registration_step',
        'current_sub_step',
        'registration_completed',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',

        "role",
         "email_verified", "email_verified_at", "status", "suspense_reason",
        "remember_token", "token_time", "two_fa", "last_login"
    ];
    // role: agent, property-owner, customer, company, lawyer, surveyor, valuer, super-add, staff: valuer, lawyer, marketer, surveyor


    protected $casts = [
        "email_verified_at" => "datetime",
        "password" => "hashed",
        "is_active" => "boolean",
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'approved_at' => 'datetime',
            'registration_completed' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(self::class, 'referred_by_id');
    }

    public function accountOfficer(): BelongsTo
    {
        return $this->belongsTo(self::class, 'account_officer_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(self::class, 'approved_by');
    }

    public function apiTokens(): HasMany
    {
        return $this->hasMany(ApiToken::class);
    }

    public function verificationCodes(): HasMany
    {
        return $this->hasMany(VerificationCode::class);
    }

    public function kycVerification(): HasOne
    {
        return $this->hasOne(UserKycVerification::class);
    }

    public function professionalProfile(): HasOne
    {
        return $this->hasOne(ProfessionalProfile::class);
    }

    public function professional(): HasOne
    {
        return $this->hasOne(Professional::class);
    }

    public function agent(): HasOne
    {
        return $this->hasOne(Agent::class);
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProfessionalDocument::class);
    }

    public function requiresAdminApproval(): bool
    {
        return in_array($this->account_type, self::PARTNER_ACCOUNT_TYPES, true);
    }

    public function isProfessionalAccount(): bool
    {
        return in_array($this->account_type, self::PROFESSIONAL_ACCOUNT_TYPES, true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->account_type === 'super-admin';
    }

    public function isStaff(): bool
    {
        return $this->account_type === 'staff';
    }

    public function canAccessDashboard(): bool
    {
        if ($this->isSuperAdmin() || $this->isStaff()) {
            return true;
        }

        if ($this->email_verified_at === null || $this->phone_verified_at === null) {
            return false;
        }

        if (!$this->registration_completed) {
            return false;
        }

        if ($this->requiresAdminApproval()) {
            return $this->approval_status === 'approved';
        }

        return true;
    }

    public function onboardingState(): array
    {
        return [
            'account_type' => $this->account_type,
            'staff_role' => $this->staff_role,
            'registration_step' => $this->registration_step,
            'current_sub_step' => $this->current_sub_step,
            'registration_completed' => (bool) $this->registration_completed,
            'email_verified' => $this->email_verified_at !== null,
            'phone_verified' => $this->phone_verified_at !== null,
            'requires_admin_approval' => $this->requiresAdminApproval(),
            'approval_status' => $this->approval_status,
            'rejection_reason' => $this->rejection_reason,
            'can_access_dashboard' => $this->canAccessDashboard(),
            'referral_code' => $this->referral_code,
            'referral_type' => $this->referral_type,
            'account_officer' => $this->accountOfficer ? [
                'id' => $this->accountOfficer->id,
                'name' => $this->accountOfficer->name,
                'staff_role' => $this->accountOfficer->staff_role,
                'email' => $this->accountOfficer->email,
                'phone' => $this->accountOfficer->phone,
            ] : null,
        ];
    }

    // public function professional() {
    //     return $this->hasOne(Professional::class, 'user_id');
    // }

    // public function agent() {
    //     return $this->hasOne(Agent::class, 'user_id');
    // }

    // public function company() {
    //     return $this->hasOne(Company::class, 'user_id');
    // }

    // public function isSuperAdmin(): bool {
    //     return $this->role === "superadmin";
    // }

    // public function isSurveyor(): bool {
    //     return $this->role === "surveyor";
    // }

    // public function isAgent(): bool {
    //     return $this->role === "agent";
    // }

    // public function isCompany(): bool {
    //     return $this->role === "company";
    // }

    // public function isLawyer(): bool {
    //     return $this->role === "lawyer";
    // }

    // public function isValuer(): bool {
    //     return $this->role === "valuer";
    // }

    // public function isProfessional(): bool {
    //     return in_array(strtolower($this->role ?? ''), [
    //         'lawyer', 'surveyor', 'valuer'
    //     ]);
    // }






}
