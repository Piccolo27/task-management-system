import { FetchOptions } from 'ofetch';
import FetchFactory from '../factory';
import {DMReplyCreate, DMReplyUpdate} from '~/types/dm';

class DmReplyModule extends FetchFactory
{
    private DM_REPLY_PATH = '/api/dm-replys'

    public create = (data: DMReplyCreate) => {
        return useAsyncData(
            'dm-reply-create',
            () => this.call(
                'POST',
                `${this.DM_REPLY_PATH}`,
                data
            )
        )
    }

    public update = (data: DMReplyUpdate) => {
        const fetchOptions: FetchOptions<'json'> = {
            headers: {
                'X-HTTP-Method-Override': 'PATCH'
            },
        }

        return useAsyncData(
            'dm-reply-update',
            () => this.call(
                'POST',
                `${this.DM_REPLY_PATH}/${data.dm_reply_id}`,
                data,
                fetchOptions
            )
        )
    }

    public delete = (dmReplyId: number) => {
        return useAsyncData(
            'dm-reply-delete',
            () => this.call(
                'DELETE',
                `${this.DM_REPLY_PATH}/${dmReplyId}`,
                undefined
            )
        )
    }
}

export default DmReplyModule;