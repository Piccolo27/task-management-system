import { Project } from "./project"

export type EmployeeValidationError = {
    employee_name: string | null
    email: string | null
    profile: string | null
    address: string | null
    phone: string | null
    position: string | null
    dob: string | null
    old_password?: string | null
    new_password?: string | null
}

export type Employee = {
    id?: string | number | undefined
    employee_id?: string
    name?: string | null
    employee_name?: string
    email: string
    profile?: string | null
    position: string | null
    dob?: string | null
    phone?: string | null
    address?: string | null,
    oldPassword?: string | null,
    newPassword?: string | null,
    confirmPassword?: string | null
    checked?: boolean,
    created_at?: string
    updated_at?: string
    deleted_at?: null | string
    project?: Project
}

export type Statistics = {
    employees?: number
    projects?: number
    not_closed_tasks?: number
    reports?: number
}