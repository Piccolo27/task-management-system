import FetchFactory from '../factory';

class DashboardModule extends FetchFactory {
    private API_ENDPOINT = '/api/dashboard/statistics'

    public getStatistics = () => {
        return useAsyncData(
            'dashboard-statistics',
            () => this.call(
                'GET',
                `${this.API_ENDPOINT}`,
                undefined
            )
        )
    }
}

export default DashboardModule;