<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\Project;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Employee $employee): bool
    {
        return $employee->position == config('constants.ADMIN');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Employee $employee, Project $project): bool
    {
        return $employee->position == config('constants.ADMIN');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Employee $employee): bool
    {
        return $employee->position == config('constants.ADMIN');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Employee $employee, Project $project): bool
    {
        return $employee->position == config('constants.ADMIN');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Employee $employee, Project $project): bool
    {
        return $employee->position == config('constants.ADMIN');
    }
}
