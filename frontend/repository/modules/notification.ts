import FetchFactory from '../factory';

class NotificationModule extends FetchFactory
{
    private NOTI_PATH = '/api/notifications'

    public getByEmployeeId = (employeeId: string) => {
        return useAsyncData(
            'notifications-by-employeeId',
            () => this.call(
                'GET',
                `${this.NOTI_PATH}/employees/${employeeId}`,
                undefined
            )
        )
    }

    public delete = (notiId: string, userId: string) => {
        return useAsyncData(
            'notifications-delete',
            () => this.call(
                'DELETE',
                `${this.NOTI_PATH}/${notiId}/employees/${userId}`,
                undefined
            )
        )
    }
}

export default NotificationModule;