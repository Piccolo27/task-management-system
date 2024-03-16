<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateEmployeeRequest;
use App\Http\Requests\Api\UpdateEmployeeRequest;
use App\Contracts\Services\EmployeeServiceInterface;

class EmployeeController extends Controller
{
    protected EmployeeServiceInterface $employeeService;

    /**
     * To create new instance for employee controller
     *
     * @param EmployeeServiceInterface $employeeService
     */
    public function __construct(EmployeeServiceInterface $employeeService)
    {
        $this->middleware("auth:api");
        $this->employeeService = $employeeService;
    }

    /**
     * To create new employee
     *
     * @param CreateEmployeeRequest $request
     * @return JsonResponse
     */
    public function createEmployee(CreateEmployeeRequest $request): JsonResponse
    {
        return $this->employeeService->createEmployee($request->validated());
    }

    /**
     * To get all employees data
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getEmployees(Request $request): JsonResponse
    {
        $role = $request->query('role');
        if ($role) {
            return $this->employeeService->getEmployeesByRole($role);
        }
        return $this->employeeService->getAllEmployees();
    }

    /**
     * To get employee by id
     *
     * @param integer $id
     * @return JsonResponse
     */
    public function getEmployee(int $id): JsonResponse
    {
        return $this->employeeService->getEmployee($id);
    }

    /**
     * To update employee by id
     *
     * @param integer $id
     * @param UpdateEmployeeRequest $request
     * @return JsonResponse
     */
    public function updateEmployee(int $id, UpdateEmployeeRequest $request): JsonResponse
    {
        return $this->employeeService->updateEmployee($id, $request->validated());
    }

    /**
     * To delete employee by id
     *
     * @param integer $id
     * @return JsonResponse
     */
    public function deleteEmployee(int $id): JsonResponse
    {
        return $this->employeeService->deleteEmployee($id);
    }
}
