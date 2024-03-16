export default defineNuxtRouteMiddleware((to, from) => {
    const unauthRoutes = ['/login', '/forgot-password', '/reset-password'];

    if (!isAuthenticated()) {
        if (!unauthRoutes.includes(to.path)) {
            return navigateTo('/login')
        }
    }

    if (unauthRoutes.includes(to.path)) {
        if (isAuthenticated()) {
            return navigateTo('/')
        }
    }
})

const isAuthenticated = () => !!useCookie('jwt-token').value