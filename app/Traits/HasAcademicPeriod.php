<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use Illuminate\Support\Facades\Auth;

trait HasAcademicPeriod
{
    protected static function bootHasAcademicPeriod(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                if (empty($model->is_superadmin_created) && !isset($model->school_id) && $user->school_id) {
                    $model->school_id = $user->school_id;
                }
                if (empty($model->branch_id) && $user->branch_id && in_array("branch_id", $model->getFillable())) {
                    $model->branch_id = $user->branch_id;
                }
                if ($user->school && empty($model->is_superadmin_created)) {
                    if (empty($model->session_id) && in_array("session_id", $model->getFillable())) {
                        $model->session_id = $user->school->current_session_id;
                    }
                    if (empty($model->term_id) && in_array("term_id", $model->getFillable())) {
                        $model->term_id = $user->school->current_term_id;
                    }
                }
            }
        });
    }
}
