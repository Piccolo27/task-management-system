import Echo from "laravel-echo";
import Pusher from "pusher-js";

declare global {
    interface Window {
        Pusher: any;
        Echo: any;
    }
}

export default defineNuxtPlugin((nuxtApp) => {
    Pusher.logToConsole = true

    const config = useRuntimeConfig()
    const jwtToken = useCookie('jwt-token').value
    const xsrfToken = useCookie('XSRF-TOKEN').value

    const echo = new Echo({
        broadcaster: 'pusher',
        key: config.public.PUSHER_APP_KEY,
        cluster: config.public.PUSHER_APP_CLUSTER,
        encrypted: true,
        forceTLS: true,
        authEndpoint: `${config.public.apiBaseUrl}/broadcasting/auth`,
        auth: {
            headers: {
                'Authorization': `Bearer ${jwtToken}`,
                'X-XSRF-TOKEN': xsrfToken as string
            }
        }
    })

    const updateToken = async (newToken: string) => {
        echo.connector.options.auth.headers['Authorization'] = `Bearer ${newToken}`
    }

    return {
        provide: {
            echo,
            updateToken
        }
    }
})