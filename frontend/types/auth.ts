import {LocationQueryValue} from "vue-router";
export interface LoginValidationErrors {
    email: string | null
    password: string | null
}

export interface ResetPasswordValidationErrors {
    email: string | null
    password: string | null
    password_confirmation: string | null
}

export type AuthUser = {
    id: number
    email: string
    name: string
    position: string
    profile: string
}

export type LoginInput = {
    email: string,
    password: string
}

export type ResetPasswordInput = {
    email: string
    password: string
    password_confirmation: string
    token: LocationQueryValue | LocationQueryValue[]
}