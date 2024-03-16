<?php

namespace App\Contracts\Repositories;

interface EmployeeRepositoryInterface
{
    public function createEmployee(array $data);
    public function updateEmployeeProfile(int $employeeId, string $fileName);
    public function getAllEmployees();
    public function getEmployees();
    public function getAdmins();
    public function getEmployee(int $employeeId);
    public function updateEmployee(int $employeeId, array $data);
    public function getEmployeeHashedPassword(int $employeeId);
    public function deleteEmployee(int $employeeId);
    public function getAdminsIdsExceptCurrent();
    public function getEmployeesTotalCount();
}
