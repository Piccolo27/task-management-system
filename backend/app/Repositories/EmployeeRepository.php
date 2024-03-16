<?php

namespace App\Repositories;

use App\Contracts\Repositories\EmployeeRepositoryInterface;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    /**
     * To insert employee data to db
     *
     * @param array $data
     * @return Employee
     */
    public function createEmployee(array $data): Employee
    {
        return Employee::create($data);
    }

    /**
     * To update employee profile data
     *
     * @param int $employeeId
     * @param string $fileName
     * @return Employee
     */
    public function updateEmployeeProfile(int $employeeId, string $fileName): Employee
    {
        $employee = Employee::findOrFail($employeeId);
        $employee->profile = $fileName;
        $employee->save();

        return $employee;
    }

    /**
     * To get all employees
     *
     * @return Collection
     */
    public function getAllEmployees(): Collection
    {
        return Employee::orderBy('employee_id')
            ->with('tasks.project')
            ->get();
    }

    /**
     * To get all employees
     *
     * @return Collection
     */
    public function getEmployees(): Collection
    {
        return Employee::where('position', config('constants.EMPLOYEE'))
            ->orderBy('employee_id')
            ->with('tasks.project')
            ->get();
    }

    /**
     * To get all admins
     *
     * @return Collection
     */
    public function getAdmins(): Collection
    {
        return Employee::where('position', config('constants.ADMIN'))
            ->orderBy('employee_id')
            ->get();
    }

    /**
     * To get employee by id
     *
     * @param int $employeeId
     * @return Employee
     */
    public function getEmployee(int $employeeId): Employee
    {
        $employee = Employee::findOrFail($employeeId);
        return $employee->makeHidden(['created_at', 'updated_at', 'deleted_at']);
    }

    /**
     * To update employee data by id
     *
     * @param integer $employeeId
     * @param array $data
     * @return Employee
     */
    public function updateEmployee(int $employeeId, array $data): Employee
    {
        $employee = Employee::findOrFail($employeeId);
        $employee->employee_name = $data['employee_name'];
        $employee->email = $data['email'];
        $employee->profile = $data['profile'] ?? $employee->profile;
        $employee->address = $data['address'];
        $employee->phone = $data['phone'];
        $employee->dob = $data['dob'];
        $employee->position = $data['position'];
        $employee->password = $data['password'] ?? $employee->password;
        $employee->save();

        return $employee;
    }

    /**
     * To get employee's hashed password by id
     *
     * @param integer $employeeId
     * @return string
     */
    public function getEmployeeHashedPassword(int $employeeId): string
    {
        return Employee::findOrFail($employeeId)->password;
    }

    /**
     * To delete employee by id
     *
     * @param integer $employeeId
     * @return Employee
     */
    public function deleteEmployee(int $employeeId): Employee
    {
        $employee = Employee::findOrFail($employeeId);
        $employee->delete();

        return $employee;
    }

    /**
     * To get all admins ids except current
     *
     * @return array
     */
    public function getAdminsIdsExceptCurrent(): array
    {
        return Employee::where('employee_id', '!=', Auth::id())
            ->where('position', config('constants.ADMIN'))
            ->pluck('employee_id')
            ->toArray();
    }

    /**
     * To get total count of employees
     *
     * @return int
     */
    public function getEmployeesTotalCount(): int
    {
        return Employee::count();
    }
}
