import { $fetch, FetchOptions } from 'ofetch';
import AuthModule from "~/repository/modules/auth";
import DmModule from '~/repository/modules/dm';
import EmployeeModule from '~/repository/modules/employee';
import DmReplyModule from "~/repository/modules/dm-reply";
import NotificationModule from "~/repository/modules/notification";
import ProjectModule from "~/repository/modules/project";
import TaskModule from "~/repository/modules/task";
import ReportModule from "~/repository/modules/report";
import DashboardModule from "~/repository/modules/dashboard";

interface IApiInstance {
    auth: AuthModule,
    employee: EmployeeModule
    dm: DmModule
    dmReply: DmReplyModule
    notification: NotificationModule
    project: ProjectModule
    task: TaskModule
    report: ReportModule
    dashboard: DashboardModule
}

export default defineNuxtPlugin((nuxtApp) => {
    const config = useRuntimeConfig()

    const fetchOptions: FetchOptions = {
        baseURL: config.public.apiBaseUrl as string
    };

    // Create a new instance of $fetcher with custom option
    const apiFetcher = $fetch.create(fetchOptions)

    // An object containing all repositories we need to expose
    const modules: IApiInstance = {
        auth: new AuthModule(apiFetcher),
        employee: new EmployeeModule(apiFetcher),
        dm: new DmModule(apiFetcher),
        dmReply: new DmReplyModule(apiFetcher),
        notification: new NotificationModule(apiFetcher),
        project: new ProjectModule(apiFetcher),
        task: new TaskModule(apiFetcher),
        report: new ReportModule(apiFetcher),
        dashboard: new DashboardModule(apiFetcher)
    };

    return {
        provide: {
            api: modules
        }
    };
});