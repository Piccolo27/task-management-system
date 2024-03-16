<?php

namespace App\Services;

use App\Contracts\Services\MailServiceInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Events\Employee\EmployeeCreated;
use App\Events\Employee\EmployeeDeleted;
use App\Events\Employee\EmployeeUpdated;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use App\Contracts\Repositories\EmployeeRepositoryInterface;
use App\Contracts\Services\EmployeeServiceInterface;

class EmployeeService implements EmployeeServiceInterface
{
    protected EmployeeRepositoryInterface $employeeRepository;
    protected MailServiceInterface $mailService;
    protected NotificationServiceInterface $notificationService;

    /**
     * To create new instance for employee service
     *
     * @param EmployeeRepositoryInterface $employeeRepository
     * @param MailServiceInterface $mailService
     * @param NotificationServiceInterface $notificationService
     */
    public function __construct(
        EmployeeRepositoryInterface  $employeeRepository,
        MailServiceInterface         $mailService,
        NotificationServiceInterface $notificationService
    ) {
        $this->employeeRepository = $employeeRepository;
        $this->mailService = $mailService;
        $this->notificationService = $notificationService;
    }

    /**
     * To create new employee in db
     *
     * @param array $data
     * @return JsonResponse
     */
    public function createEmployee(array $data): JsonResponse
    {
        $employee = $data;
        $employee['profile'] = 'dummy.png';

        DB::beginTransaction();

        try {
            $employee = $this->employeeRepository->createEmployee($employee);
            $fileName = $this->getFileNameByEmployeeId($data['profile'], $employee->employee_id);
            $this->storeProfile($data['profile'], $fileName);
            $newEmployee = $this->employeeRepository->updateEmployeeProfile($employee->employee_id, $fileName);
            $this->mailService->sendEmployeeCreateMail($newEmployee);
            DB::commit();
            $this->sendEmployeeCreatedNotification($newEmployee);
            $response = response()->json(['message' => config('constants.success.EMPLOYEE_CREATE')], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("An exception occurred in creating new employee: " . $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.EMPLOYEE_CREATE')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * To send employee created notification to client side
     *
     * @param Employee $employee
     * @return void
     */
    private function sendEmployeeCreatedNotification(Employee $employee): void
    {
        $data = [
            'message' => Auth::user()->employee_name . ' created a new employee named ' . $employee->employee_name,
            'created_by' => Auth::id()
        ];
        broadcast(new EmployeeCreated($data));
    }

    /**
     * To update employee by id
     *
     * @param integer $employeeId
     * @param array $data
     * @return JsonResponse
     */
    public function updateEmployee(int $employeeId, array $data): JsonResponse
    {
        if ($employeeId === Auth::user()->employee_id && $data['new_password'] && !$data['old_password']) {
            return response()->json([
                'errors' => [
                    'old_password' => [config('constants.errors.old_password.REQUIRED')]
                ]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            if (isset($data['new_password'])) {
                $data['password'] = $this->validateAndHashPassword($employeeId, $data);
            }

            if ($data['profile']) {
                $fileName = $this->getFileNameByEmployeeId($data['profile'], $employeeId);
                $this->deleteOldProfile($fileName);
                $this->storeProfile($data['profile'], $fileName);
                $data['profile'] = $fileName;
            }
            $updatedEmployee = $this->employeeRepository->updateEmployee($employeeId, $data);
            $this->sendEmployeeUpdatedNotification($updatedEmployee);
            $response = response()->json(['message' => config('constants.success.EMPLOYEE_UPDATE')], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("An exception occurred in updating employee by id: " . $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.EMPLOYEE_UPDATE')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * To validate old password and get new hashed password
     *
     * @param integer $employeeId
     * @param array $data
     * @return string | null
     */
    private function validateAndHashPassword(int $employeeId, array $data): string | null
    {
        if ($data['old_password']) {
            if (!Hash::check($data['old_password'], $this->employeeRepository->getEmployeeHashedPassword($employeeId))) {
                return null;
            }
        }

        return Hash::make($data['new_password']);
    }

    /**
     * To generate file name by employee id
     *
     * @param $file
     * @param int $employeeId
     * @return string
     */
    private function getFileNameByEmployeeId($file, int $employeeId): string
    {
        return 'employees/' . $employeeId . '/profile.' . $file->getClientOriginalExtension();
    }

    /**
     * To delete old profile image when updated
     *
     * @param string $fileName
     * @return void
     */
    private function deleteOldProfile(string $fileName): void
    {
        Storage::disk("public")->delete($fileName);
    }

    /**
     * To store profile image in local
     *
     * @param $file
     * @param string $fileName
     * @return void
     */
    private function storeProfile($file, string $fileName): void
    {
        Storage::disk('public')->putFileAs('', $file, $fileName);
    }

    /**
     * To send employee updated notification to client side
     *
     * @param Employee $employee
     * @return void
     */
    private function sendEmployeeUpdatedNotification(Employee $employee): void
    {
        $data = [
            'message' => Auth::user()->employee_name . ' updated an employee named ' . $employee->employee_name,
            'created_by' => Auth::id()
        ];
        broadcast(new EmployeeUpdated($data));
    }

    /**
     * To get employee by id
     *
     * @param int $employeeId
     * @return JsonResponse
     */
    public function getEmployee(int $employeeId): JsonResponse
    {
        try {
            $employee = $this->employeeRepository->getEmployee($employeeId);
            $response = response()->json(['employee' => $employee], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("An exception occurred in getting employee by id: " . $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.EMPLOYEE_GET')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * To get all employees
     *
     * @return JsonResponse
     */
    public function getAllEmployees(): JsonResponse
    {
        try {
            $employees = $this->employeeRepository->getAllEmployees();
            $response = response()->json(['employees' => $employees], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("An exception occurred in getting all employees: ". $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.EMPLOYEES_GET')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * To get employees by role
     *
     * @param string $role
     * @return JsonResponse
     */
    public function getEmployeesByRole(string $role): JsonResponse
    {
        try {
            if ($role === 'admin') {
                $employees = $this->employeeRepository->getAdmins();
            } else if ($role === 'employee') {
                $employees = $this->employeeRepository->getEmployees();
            } else {
                $employees = [];
            }
            $response = response()->json(['employees' => $employees], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("An exception occurred in getting employees: ". $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.EMPLOYEES_GET')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * To delete employee by id
     *
     * @param integer $employeeId
     * @return JsonResponse
     */
    public function deleteEmployee(int $employeeId): JsonResponse
    {
        try {
            $deletedEmployee = $this->employeeRepository->deleteEmployee($employeeId);
            $this->deleteImageInStorage($employeeId);
            $this->sendEmployeeDeletedNotification($deletedEmployee);
            $response = response()->json(['message' => config('constants.success.EMPLOYEE_DELETE')], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("An exception occurred in deleting employees: ". $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.EMPLOYEE_DELETE')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * To send employee deleted notification to client side
     *
     * @param Employee $employee
     * @return void
     */
    private function sendEmployeeDeletedNotification(Employee $employee): void
    {
        $data = [
            'message' => Auth::user()->employee_name . ' deleted an employee named ' . $employee->employee_name,
            'created_by' => Auth::id()
        ];
        broadcast(new EmployeeDeleted($data));
    }

    /**
     * To remove employee profile
     *
     * @param integer $employeeId
     * @return void
     */
    public function deleteImageInStorage(int $employeeId): void
    {
        Storage::disk("public")->deleteDirectory('employees/' . $employeeId);
    }

    /**
     * To get employees total count
     *
     * @return int
     */
    public function getEmployeesTotalCount(): int
    {
        return $this->employeeRepository->getEmployeesTotalCount();
    }
}
