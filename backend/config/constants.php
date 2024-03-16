<?php

return [
    "ADMIN" => 0,
    "EMPLOYEE" => 1,
    "REPLYABLE" => 1,
    "success" => [
        "PASSWORD_RESET" => "User account password reset successfully",
        "PASSWORD_RESET_LINK" => "Reset password link sent successfully. Check your email",
        "EMPLOYEE_CREATE" => "Employee created successfully",
        "EMPLOYEE_UPDATE" => "Employee updated successfully",
        "EMPLOYEE_DELETE" => "Employee deleted successfully",
        "DM_CREATE" => "Direct message created successfully",
        "DM_RP_CREATE" => "Direct message reply created successfully",
        "PROJECT_CREATE" => "Project created successfully",
        "PROJECT_UPDATE" => "Project updated successfully",
        "PROJECT_DELETE" => "Project deleted successfully",
        "TASK_CREATE" => "Task created successfully",
        "TASK_UPDATE" => "Task updated successfully",
        "TASK_DELETE" => "Task deleted successfully",
        "REPORT_CREATE" => "Report created successfully",
        "REPORT_UPDATE" => "Report updated successfully",
        "REPORT_DELETE" => "Report deleted successfully",
        "NOTIFICATION_DELETE" => "Notification removed successfully",
    ],
    "errors" => [
        "name" => [
            "REQUIRED" => "Name is required",
            "INVALID" => "Name is invalid",
            "MAX" => "Name must be less than 255 characters",
        ],
        "email" => [
            "REQUIRED" => "Email is required",
            "INVALID" => "Email is invalid",
            "INCORRECT" => "Email is incorrect",
            "UNIQUE" => "Employee with email already exist",
            "MAX" => "Email must be less than 255 characters",
        ],
        "password" => [
            "REQUIRED" => "Password is required",
            "INVALID" => "Password is invalid",
            "CONFIRMED" => "Passwords aren't same"
        ],
        "token" => [
            "REQUIRED" => "Token is required",
            "INVALID" => "Token is invalid",
        ],
        "profile" => [
            "REQUIRED" => "Profile is required",
            "FILE" => "Profile must be file type",
            "MIMES" => "Profile must be jpg,png",
            "MAX" => "Profile size must be less than 5MB",
        ],
        "address" => [
            "INVALID" => "Address is invalid",
            "MAX" => "Address must be less than 255 characters",
        ],
        "phone" => [
            "NUMERIC" => "Phone number must be numeric",
            "DIGITS_BETWEEN" => "Phone number must be maximum 11 characters",
        ],
        "dob" => [
            "INVALID" => "Date of birth is invalid",
        ],
        "position" => [
            "REQUIRED" => "Position is required",
            "INVALID" => "Position is invalid",
            "LIMIT" => "Position must be admin or member"
        ],
        "old_password" => [
            "REQUIRED" => "Old password is required",
            "INVALID" => "Old password is invalid",
            "INCORRECT" => "Old password is incorrect"
        ],
        "new_password" => [
            "INVALID" => "New password is invalid",
            "CONFIRM" => "New password and confirm password must be same"
        ],
        "dm_title" => [
            "REQUIRED" => "Dm title is required",
            "INVALID" => "Dm title is invalid",
            "MAX" => "Dm title must be less than 100 characters"
        ],
        "dm_text" => [
            "REQUIRED" => "Dm text is required",
            "INVALID" => "Dm text is invalid",
            "MAX" => "Dm text must be less than 30000 characters"
        ],
        "dm_selected_person" => [
            "REQUIRED" => "Target person must be selected at least one",
            "INVALID" => "Dm selected person is invalid",
        ],
        "dm_reservation_transmission_dt" => [
            "INVALID" => "Dm reservation and transmission datetime is invalid"
        ],
        "project" => [
            "name" => [
                "REQUIRED" => "Project name is required",
                "INVALID" => "Project name is invalid",
                "MAX" => "Project name must be less than 256 characters"
            ],
            "language" => [
                "REQUIRED" => "Language is required",
                "INVALID" => "Language is invalid",
                "MAX" => "Language must be less than 256 characters"
            ],
            "description" => [
                "INVALID" => "Description is invalid",
                "MAX" => "Description must be less than 256 characters"
            ],
            "startDate" => [
                "DATE" => "Start date must be in date format"
            ],
            "endDate" => [
                "DATE" => "End date must be in date format"
            ]
        ],
        "task" => [
            "project_id" => [
                "REQUIRED" => "Project ID is required",
                "INVALID" => "Project ID is invalid",
                "EXIST" => "Project ID doesn't exist"
            ],
            "title" => [
                "REQUIRED" => "Task title is required",
                "INVALID" => "Task title is invalid",
                "MAX" => "Task title must be less than 256 characters"
            ],
            "description" => [
                "REQUIRED" => "Task description is required",
                "INVALID" => "Task description is invalid",
                "MAX" => "Task description must be less than 256 characters"
            ],
            "assigned_member_id" => [
                "REQUIRED" => "Assigned member ID is required",
                "INVALID" => "Assigned member ID is invalid",
                "EXIST" => "Assigned member ID doesn't exist"
            ],
            "estimate_hr" => [
                "REQUIRED" => "Estimated hour is required",
                "INVALID" => "Estimated hour is invalid",
            ],
            "actual_hr" => [
                "INVALID" => "Actual hour is invalid",
            ],
            "status" => [
                "REQUIRED" => "Status is required",
                "INVALID" => "Status is invalid"
            ],
            "estimate_start_date" => [
                "DATE" => "Estimated start date must be in date format"
            ],
            "estimate_finish_date" => [
                "DATE" => "Estimated end date must be in date format"
            ],
            "actual_start_date" => [
                "DATE" => "Actual start date must be in date format"
            ],
            "actual_finish_date" => [
                "DATE" => "Actual end date must be in date format"
            ]
        ],
        "report" => [
            "report_to" => [
                "REQUIRED" => "You need to select an admin to report",
                "INVALID" => "Invalid admin",
                "EXIST" => "The admin you choice doesn't exist"
            ],
            "description" => [
                "REQUIRED" => "Description is required",
                "INVALID" => "Description is invalid",
                "MAX" => "Description must be less than 256 characters"
            ]
        ],
        "EMPLOYEE_CREATE" => "Creating employee process failed.",
        "EMPLOYEE_UPDATE" => "Updating employee process failed.",
        "EMPLOYEE_GET" => "Getting employee information failed",
        "EMPLOYEE_DELETE" => "Deleting employee process failed",
        "EMPLOYEES_GET" => "Getting employees data failed",
        "DM_CREATE" => "Creating direct message process failed.",
        "DMS_GET" => "Getting direct messages process failed",
        "DM_GET" => "Getting direct message process failed",
        "DM_RP_CREATE" => "Creating direct message reply process failed.",
        "DM_RP_UPDATE" => "Updating direct message reply process failed.",
        "DM_RP_DELETE" => "Deleting direct message reply process failed.",
        "NOTIFICATION_GET" => "Getting notifications process failed",
        "NOTIFICATION_DELETE" => "Deleting notification process failed",
        "PROJECT_CREATE" => "Creating project failed",
        "PROJECT_DELETE" => "Deleting project failed",
        "PROJECT_UPDATE" => "Updating project failed",
        "PROJECT_GET" => "Getting project failed",
        "PROJECTS_GET" => "Getting all projects failed",
        "TASKS_GET" => "Getting all tasks failed",
        "TASK_CREATE" => "Creating task failed",
        "TASK_UPDATE" => "Updating task failed",
        "TASK_DELETE" => "Deleting task failed",
        "TASK_GET" => "Getting task failed",
        "REPORT_CREATE" => "Creating report failed",
        "REPORTS_GET" => "Getting reports failed",
        "INCORRECT_EMAIL_PASSWORD"=> "Email or password is incorrect",
        "SOMETHING_WRONG" => "Something went wrong!",
        "UNAUTHORIZED_REQUEST" => "Unauthorized request!"
    ],
    "TASK_OPEN_STATUS" => 0,
    "TASK_IN_PROGRESS_STATUS" => 1,
    "TASK_FINISHED_STATUS" => 2,
    "TASK_CLOSED_STATUS" => 3
];
