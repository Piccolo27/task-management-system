import {Project} from "~/types/project";
import {Employee} from "~/types/employee";

export type Task = {
    task_id?: number
    project_id?: number | null
    title: string
    description: string
    assigned_member_id: number | null
    estimate_hr: number | null
    actual_hr?: number | null
    status?: number | null | string
    estimate_start_date?: string | null
    estimate_finish_date?: string | null
    actual_start_date?: string | null
    actual_finish_date?: string | null
    created_at?: string
    updated_at?: string,
    project?: Project | {},
    member?: Employee | {}
}

export type TaskValidationError = {
    project_id: string | null,
    title: string | null,
    description: string | null,
    assigned_member_id: string | null,
    estimate_hr: string | null,
    actual_hr?: string | null,
    estimate_start_date: string | null,
    estimate_finish_date: string | null,
    status?: string | null,
    actual_start_date?: string | null,
    actual_finish_date?: string | null
}