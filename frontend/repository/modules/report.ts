import { FetchOptions } from 'ofetch';
import FetchFactory from '../factory';
import {Report} from "~/types/report";

class ReportModule extends FetchFactory
{
    private REPORT_PATH = '/api/reports'
    private BASE_URL = useRuntimeConfig().public.apiBaseUrl
    private excelFileExportInProgress = false

    public create = (data: Report) => {
        return useAsyncData(
            'report-create',
            () => this.call(
                'POST',
                `${this.REPORT_PATH}`,
                data
            )
        )
    }

    public get = () => {
        return useAsyncData(
            'reports',
            () => this.call(
                'GET',
                `${this.REPORT_PATH}`,
                undefined
            )
        )
    }

    public export = () => {
        if (this.excelFileExportInProgress) return
        this.excelFileExportInProgress = true

        const jwtToken = useCookie('jwt-token').value as string
        const csrfToken =  useCookie('XSRF-TOKEN').value as string

        fetch(`${this.BASE_URL + this.REPORT_PATH}/export`, {
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
            link.setAttribute('download', 'report.xlsx');
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

export default ReportModule;