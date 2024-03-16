import { FetchOptions } from 'ofetch';
import FetchFactory from '../factory';
import {DMCreate, DMUpdate} from '~/types/dm';

class DmModule extends FetchFactory
{
    private DM_PATH = '/api/dms'

    public create = (data: DMCreate) => {
        return useAsyncData(
            'dm-create',
            () => this.call(
                'POST',
                `${this.DM_PATH}`,
                data
            )
        )
    }

    public getAll = () => {
        return useAsyncData(
            'dms',
            () => this.call(
                'GET',
                `${this.DM_PATH}`,
                undefined
            )
        )
    }

    public get = (dmID: string) => {
        return useAsyncData(
            'dm',
            () => this.call(
                'GET',
                `${this.DM_PATH + '/' + dmID}`,
                undefined
            )
        )
    }

    public update = (data: DMUpdate, dmId: string) => {
        const fetchOptions: FetchOptions<'json'> = {
            headers: {
                'X-HTTP-Method-Override': 'PATCH'
            },
        }

        return useAsyncData(
            'dm-update',
            () => this.call(
                'POST',
                `${this.DM_PATH + '/' + dmId}`,
                data,
                fetchOptions
            )
        )
    }
}

export default DmModule;