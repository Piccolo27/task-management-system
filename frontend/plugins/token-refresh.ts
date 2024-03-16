import {useJwt} from "@vueuse/integrations/useJwt";

export default defineNuxtPlugin(() => {
    const {refreshJwtToken} = useUtils()

    const jwtToken = useCookie('jwt-token')
    let refreshInterval: NodeJS.Timeout;

    watch(jwtToken, (newValue, oldValue) => {
        if (newValue) {
            const {payload} = useJwt(newValue)
            const expiryTimestampInMs = (payload.value?.exp as number) * 1000
            const initTimestampInMs = Date.now()
            const tokenRefreshTimeInMs = (expiryTimestampInMs - initTimestampInMs) - 5000

            if (refreshInterval) clearInterval(refreshInterval)

            if (tokenRefreshTimeInMs > 0) {
                refreshInterval = setInterval(refreshJwtToken, tokenRefreshTimeInMs)
            }
        } else {
            if (refreshInterval) clearInterval(refreshInterval)
        }
    }, { immediate: true })
})