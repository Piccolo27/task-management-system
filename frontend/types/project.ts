export type Project = {
    project_id?: number
    project_name: string
    language: string
    description?: string
    start_date?: string
    end_date?: string
    created_at?: string
    updated_at?: string
    deleted_at?: string
}

export type ProjectValidationError = {
    project_name?: string
    language?: string
    description?: string
    start_date?: string
    end_date?: string
}