import { Employee } from "./employee"

export type DM = {
    direct_message_id: number
    owner_id: number
    title: string
    body: string
    replyable: number
    start_at: string
    created_by: number
    updated_by: number
    created_at: string
    updated_at: string,
    owner: Employee
    dm_thread: DMThread
}

export type DMThread = {
    dm_thread_id: number
    direct_message_id: number
    replyable: number
    owner_unread: number
    user_unread: number
    dm_updated: number
    created_by: number
    dm_replys: DMReply[]
    members: Employee[]
}

export type DMReply = {
    dm_reply_id: number
    dm_thread_id: number
    body: string
    created_by: number
    updated_by: number
    created_user: Employee
    created_at: string
    updated_at: string
}

export type DMCreate = {
    title: string
    text: string
    replyable: boolean
    selectedPerson: string[]
    reservationAndTransmissionDT: string | null
}

export type DMUpdate = {
    title: string
    text: string
    replyable: boolean
    selectedPerson: string[]
    reservationAndTransmissionDT: string | null
}

export type DMReplyCreate = {
    body: string
    dm_thread_id: number
}

export type DMReplyUpdate = {
    body: string
    dm_thread_id: number
    dm_reply_id: number
}

export type DmCreateValidationErrors = {
    title: string
    text: string
    selectedPerson: string
    reservationAndTransmissionDT: string
}