import { FetchOptions } from 'ofetch';
import FetchFactory from '../factory';
import {Project} from "~/types/project";

class ProjectModule extends FetchFactory
{
    private PROJECT_PATH = '/api/projects'

    public create = (data: Project) => {
        return useAsyncData(
            'project-create',
            () => this.call(
                'POST',
                `${this.PROJECT_PATH}`,
                data
            )
        )
    }

    public get = () => {
        return useAsyncData(
            'projects-get-all',
            () => this.call(
                'GET',
                `${this.PROJECT_PATH}`,
                undefined
            )
        )
    }

    public getOne = (projectId: string) => {
        return useAsyncData(
            'projects-get-one',
            () => this.call(
                'GET',
                `${this.PROJECT_PATH + '/' + projectId}`,
                undefined
            )
        )
    }

    public update = (data: Project) => {
        const fetchOptions: FetchOptions<'json'> = {
            headers: {
                'X-HTTP-Method-Override': 'PATCH'
            }
        }

        return useAsyncData(
            'project-update',
            () => this.call(
                'POST',
                `${this.PROJECT_PATH + '/' + data.project_id}`,
                data,
                fetchOptions
            )
        )
    }

    public delete = (projectId: string) => {
        return useAsyncData(
            'project-delete',
            () => this.call(
                'DELETE',
                `${this.PROJECT_PATH + '/' + projectId}`,
                undefined
            )
        )
    }
}

export default ProjectModule;