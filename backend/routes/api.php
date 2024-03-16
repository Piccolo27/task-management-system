<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DmController;
use App\Http\Controllers\Api\DmReplyController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['api'])->group(function () {
    // auth route
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class,'sendResetPasswordLink']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::get('refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('logout', [AuthController::class, 'logout']);

    // dashboard
    Route::get('dashboard/statistics', [DashboardController::class, 'getDashboardStatistics']);

    // employee
    Route::get('employees', [EmployeeController::class, 'getEmployees']);
    Route::get('employees/{id}', [EmployeeController::class, 'getEmployee']);
    Route::post('employees', [EmployeeController::class, 'createEmployee']);
    Route::patch('employees/{id}', [EmployeeController::class,'updateEmployee']);
    Route::delete('employees/{id}', [EmployeeController::class,'deleteEmployee']);

    // direct message
    Route::get('dms', [DmController::class,'getDms']);
    Route::get('dms/{id}', [DmController::class, 'getDm']);
    Route::post('dms', [DmController::class, 'createDm']);
    Route::patch('dms/{dm}', [DmController::class, 'updateDm']);

    // direct message replys
    Route::post('dm-replys', [DmReplyController::class, 'createDmReply']);
    Route::patch('dm-replys/{dmReply}', [DmReplyController::class, 'updateDmReply']);
    Route::delete('dm-replys/{dmReply}', [DmReplyController::class, 'deleteDmReply']);

    // notifications
    Route::get('notifications/employees/{employeeId}', [NotificationController::class, 'getNotificationsByEmployeeId']);
    Route::delete('notifications/{notiId}/employees/{employeeId}', [NotificationController::class, 'deleteNotificationForEmployee']);

    // projects
    Route::get('projects', [ProjectController::class, 'getProjects']);
    Route::get('projects/{project}', [ProjectController::class, 'getProject']);
    Route::post('projects', [ProjectController::class, 'createProject']);
    Route::patch('projects/{project}', [ProjectController::class, 'updateProject']);
    Route::delete('projects/{project}', [ProjectController::class, 'deleteProject']);

    // tasks
    Route::get('tasks', [TaskController::class, 'getTasks']);
    Route::get('tasks/export', [TaskController::class, 'exportTasks']);
    Route::get('tasks/{task}', [TaskController::class, 'getTask']);
    Route::post('tasks', [TaskController::class, 'createTask']);
    Route::patch('tasks/{task}', [TaskController::class, 'updateTask']);
    Route::delete('tasks/{task}', [TaskController::class, 'deleteTask']);

    // reports
    Route::get('reports', [ReportController::class, 'getReports']);
    Route::post('reports', [ReportController::class, 'createReport']);
    Route::get('reports/export', [ReportController::class, 'exportReports']);
});
