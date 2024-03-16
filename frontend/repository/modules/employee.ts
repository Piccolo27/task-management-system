import { FetchOptions } from 'ofetch';
import FetchFactory from '../factory';

class EmployeeModule extends FetchFactory
{
    private EMPLOYEE_PATH = '/api/employees'

    public create = (data: FormData) => {
        return useAsyncData(
            'employee-create',
            () => this.call(
                'POST',
                `${this.EMPLOYEE_PATH}`,
                data
            )
        )
    }

    public getEmployees = () => {
        return useAsyncData(
            'employees',
            () => this.call(
                'GET',
                `${this.EMPLOYEE_PATH}?role=employee`,
                undefined
            )
        )
    }

    public getAdmins = () => {
        return useAsyncData(
            'admins',
            () => this.call(
                'GET',
                `${this.EMPLOYEE_PATH}?role=admin`,
                undefined
            )
        )
    }

    public getEmployee = (employeeId: string) => {
        return useAsyncData(
            `employee:${employeeId}`,
            () => this.call(
                'GET',
                `${this.EMPLOYEE_PATH + '/' + employeeId}`,
                undefined
            )
        )
    }

    public update = (data: FormData, employeeId: string) => {
        const fetchOptions: FetchOptions<'json'> = {
            headers: {
                'X-HTTP-Method-Override': 'PATCH'
            }
        }

        return useAsyncData(
            'employee-update',
            () => this.call(
                'POST',
                `${this.EMPLOYEE_PATH + '/' + employeeId}`,
                data,
                fetchOptions
            )
        )
    }

    public delete = (employeeId: string) => {
        return useAsyncData(
            'employee-delete',
            () => this.call(
                'DELETE',
                `${this.EMPLOYEE_PATH + '/' + employeeId}`,
                undefined
            )
        )
    }
}

export default EmployeeModule;