export default defineNuxtPlugin(async (nuxtApp) => {
    const { initAuth } = useAuth();

    // Initialize auth state from localStorage before app mounts
    initAuth();
});
