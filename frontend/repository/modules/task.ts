import { FetchOptions } from 'ofetch';
import FetchFactory from '../factory';
import {Task} from "~/types/task";

class TaskModule extends FetchFactory
{
    private TASK_PATH = '/api/tasks'
    private BASE_URL = useRuntimeConfig().public.apiBaseUrl
    private excelFileExportInProgress = false

    public create = (data: Task) => {
        return useAsyncData(
            'task-create',
            () => this.call(
                'POST',
                `${this.TASK_PATH}`,
                data
            )
        )
    }

    public get = (status: string | null = null) => {
        const endpoint = status == null ? this.TASK_PATH : `${this.TASK_PATH}?status=${status}`
        return useAsyncData(
            'tasks-get',
            () => this.call(
                'GET',
                endpoint,
                undefined
            )
        )
    }

    public getOne = (taskId: string) => {
        return useAsyncData(
            'task-get',
            () => this.call(
                'GET',
                `${this.TASK_PATH}/${taskId}`,
                undefined
            )
        )
    }

    public update = (taskId: string, data: Task) => {
        const fetchOptions: FetchOptions<'json'> = {
            headers: {
                'X-HTTP-Method-Override': 'PATCH'
            }
        }

        return useAsyncData(
            'task-update',
            () => this.call(
                'POST',
                `${this.TASK_PATH}/${taskId}`,
                data,
                fetchOptions
            )
        )
    }

    public export = () => {
        if (this.excelFileExportInProgress) return
        this.excelFileExportInProgress = true

        const jwtToken = useCookie('jwt-token').value as string
        const csrfToken =  useCookie('XSRF-TOKEN').value as string

        fetch(`${this.BASE_URL + this.TASK_PATH}/export`, {
            method: 'GET',
            headers: {
                'Accept': '*/*',
                'X-XSRF-TOKEN': csrfToken,
                'Authorization': `Bearer ${jwtToken}`,
            },
            credentials: 'include',
        }).then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.blob();
        }) .then(blob => {
            const blobUrl = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = blobUrl;
            link.setAttribute('download', 'tasks.xlsx');
            document.body.appendChild(link);
            link.click();

            link.parentNode?.removeChild(link);
            window.URL.revokeObjectURL(blobUrl);
        }).catch(error => {
            console.error(error)
        }).finally(() => {
            this.excelFileExportInProgress = false
        })
    }
}

export default TaskModule