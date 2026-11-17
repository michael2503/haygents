<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();

        // Superadmin has global visibility across all schools
        if ($user->role === "superadmin") {
            return;
        }

        // Apply school_id restriction
        if ($user->school_id && in_array("school_id", $model->getFillable())) {
            $builder->where($model->getTable() . ".school_id", $user->school_id);
        }

        // Apply branch_id restriction if branch-scoped role
        $branchRoles = ["branch_manager", "teacher", "accountant", "librarian", "driver", "security", "store_manager"];
        if (in_array($user->role, $branchRoles) && $user->branch_id && in_array("branch_id", $model->getFillable())) {
            $builder->where($model->getTable() . ".branch_id", $user->branch_id);
        }
    }
}
