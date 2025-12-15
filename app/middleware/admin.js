export default defineNuxtRouteMiddleware((to, from) => {
    const { user, isLoggedIn } = useAuth();

    // If not logged in or not admin, redirect to home
    if (!isLoggedIn.value || user.value?.role !== 'admin') {
        return navigateTo('/');
    }
});
