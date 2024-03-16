import FetchFactory from '../factory';
import {LoginInput, ResetPasswordInput} from "~/types/auth";

class AuthModule extends FetchFactory {
    private LOGIN_PATH = '/api/login'
    private LOGOUT_PATH = '/api/logout'
    private FORGOT_PASSWORD_PATH = '/api/forgot-password'
    private RESET_PASSWORD_PATH = '/api/reset-password'
    private JWT_REFRESH_PATH = '/api/refresh-token'
    private SANCTUM_PATH = 'http://localhost:8000/sanctum/csrf-cookie'

    public login = async (credentials: LoginInput) => {
        await this.storeCSRFTokenInCookie()
        return this.performLogin(credentials)
    }

    public logout = () => {
        return useAsyncData(
            'logout',
            () => this.call(
                'POST',
                `${this.LOGOUT_PATH}`,
                undefined
            )
        )
    }

    public forgotPassword = async (email: string) => {
        await this.storeCSRFTokenInCookie()

        return useAsyncData(
            'forgot-password',
            () => this.call(
                'POST',
                `${this.FORGOT_PASSWORD_PATH}`,
                {email}
            )
        )
    }

    public resetPassword = async (credentials: ResetPasswordInput) => {
        await this.storeCSRFTokenInCookie()

        return useAsyncData(
            'reset-password',
            () => this.call(
                'POST',
                `${this.RESET_PASSWORD_PATH}`,
                credentials
            )
        )
    }

    private storeCSRFTokenInCookie = async () => {
        await useFetch(this.SANCTUM_PATH, {
            credentials: "include",
        })
    }

    private performLogin = (credentials: LoginInput) => {
        return useAsyncData(
            'login',
            () => this.call(
                'POST',
                `${this.LOGIN_PATH}`,
                credentials
            )
        )
    }

    public refreshJwtToken = () => {
        return useAsyncData(
            'jwt-refresh',
            () => this.call(
                'GET',
                `${this.JWT_REFRESH_PATH}`,
                undefined
            )
        )
    }
}

export default AuthModule;