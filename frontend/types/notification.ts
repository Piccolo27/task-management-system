import {Employee} from "~/types/employee";

export type Notification = {
    id?: number
    message: string
    created_by: number
    created_employee: Employee
    created_at: string
    updated_at?: string
    report_to?: number
    task_member_id?: number
}