export const useUtils = () => {
    const logout = async() => {
        const { $api } = useNuxtApp()

        const clearAuhCookies = () => {
            useCookie('jwt-token').value = null
            useCookie('username').value = null
        }

        await $api.auth.logout()
        clearAuhCookies()
        return navigateTo('/login')
    }

    const getSessionStorage = (key: string) => {
        return sessionStorage.getItem(key)
    }

    const clearSessionStorage = (key: string) => {
        const item = getSessionStorage(key)
        if (item !== null) sessionStorage.removeItem(key)
        return
    }

    const truncateString = (str: string) => {
        const maxLength = 50;
        if (str.length > maxLength) {
            return str.substring(0, maxLength) + '...';
        }
        return str;
    }

    const refreshJwtToken = async () => {
        const {$api, $updateToken} = useNuxtApp()
        const { data, error } = await $api.auth.refreshJwtToken()

        if (error.value) console.error("Failed to refresh the token", error.value)
        if (data.value) {
            useCookie('jwt-token').value = data.value.access_token
            $updateToken(data.value.access_token)
        }
    }

    const compareStrings = (a: string, b: string) => a.localeCompare(b);
    const compareDates = (a: string | number | Date, b: string | number | Date) => (new Date(a) as any) - (new Date(b) as any);
    const compareNumbers = (a: number, b: number) => a - b;

    const formatDateTime = (datetimeObj: Date) => datetimeObj.toISOString().split('T')[0]

    return {
        logout,
        getSessionStorage,
        clearSessionStorage,
        truncateString,
        refreshJwtToken,
        compareStrings,
        compareDates,
        compareNumbers,
        formatDateTime
    }
}