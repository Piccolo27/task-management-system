<?php

namespace App\Contracts\Services;

interface EmployeeServiceInterface
{
    public function createEmployee(array $data);
    public function getAllEmployees();
    public function getEmployeesByRole(string $role);
    public function getEmployee(int $employeeId);
    public function updateEmployee(int $employeeId, array $data);
    public function deleteEmployee(int $employeeId);
    public function getEmployeesTotalCount();
}
