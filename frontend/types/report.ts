import {Employee} from "~/types/employee";

export type ReportValidationError = {
    report_to: string | null
}

export type Report = {
    report_id?: number | null
    date?: string | null
    report_to?: number | string | null
    reported_by?: null
    admin?: Employee,
    reporter?: Employee,
    task_id?: number | null
    task_title?: string | null
    project_name?: string | null
    percentage?: number | null
    task_type?: string | null
    task_status?: string | null
    hour?: number | null
    description?: string
}