/**
 * Composable for handling authentication state and API calls
 */
export const useAuth = () => {
    const api = useApi()

    // Global state using Nuxt's useState
    const user = useState('auth_user', () => null)
    const isLoggedIn = useState('isLoggedIn', () => false)
    const showLoginModal = useState('showLoginModal', () => false)
    const showRegisterModal = useState('showRegisterModal', () => false)
    const authLoading = useState('authLoading', () => false)
    const authError = useState('authError', () => null)
    const authSuccessMessage = useState('authSuccessMessage', () => null)

    /**
     * Initialize auth state from localStorage
     */
    const initAuth = () => {
        if (process.client) {
            const token = localStorage.getItem('auth_token')
            const storedUser = localStorage.getItem('auth_user')

            if (token && storedUser) {
                try {
                    user.value = JSON.parse(storedUser)
                    isLoggedIn.value = true
                } catch (e) {
                    // Invalid stored user, clear it
                    localStorage.removeItem('auth_token')
                    localStorage.removeItem('auth_user')
                }
            }
        }
    }

    /**
     * Register a new user
     */
    const register = async (name, email, phone, password, passwordConfirmation) => {
        authLoading.value = true
        authError.value = null

        const { data, error } = await api.post('/register', {
            name,
            email,
            phone,
            password,
            password_confirmation: passwordConfirmation
        })

        authLoading.value = false

        if (error) {
            authError.value = error.message || 'Registration failed'
            return false
        }

        // Store token and user
        if (process.client && data.token) {
            localStorage.setItem('auth_token', data.token)
            localStorage.setItem('auth_user', JSON.stringify(data.user))
        }

        user.value = data.user

        if (data.token) {
            isLoggedIn.value = true
        }
        // Don't close modal automatically, let the component handle the success message
        // showRegisterModal.value = false
        // showLoginModal.value = false

        return { success: true, message: data.message }
    }

    /**
     * Login user
     */
    const login = async (email, password) => {
        authLoading.value = true
        authError.value = null

        const { data, error } = await api.post('/login', {
            email,
            password
        })

        authLoading.value = false

        if (error) {
            authError.value = error.message || 'Invalid credentials'
            return false
        }

        // Store token and user
        if (process.client) {
            localStorage.setItem('auth_token', data.token)
            localStorage.setItem('auth_user', JSON.stringify(data.user))
        }

        user.value = data.user
        isLoggedIn.value = true


        return true
    }

    /**
     * Logout user
     */
    const logout = async () => {
        authLoading.value = true

        // Call logout API (optional, just invalidates token on server)
        await api.post('/logout')

        // Clear local state
        if (process.client) {
            localStorage.removeItem('auth_token')
            localStorage.removeItem('auth_user')
        }

        user.value = null
        isLoggedIn.value = false
        authLoading.value = false
    }

    /**
     * Get current user from API
     */
    const fetchUser = async () => {
        const { data, error } = await api.get('/me')

        if (!error && data) {
            user.value = data.user
            isLoggedIn.value = true
            if (process.client) {
                localStorage.setItem('auth_user', JSON.stringify(data.user))
            }
        }

        return data?.user
    }

    /**
     * Modal controls
     */
    const openLoginModal = () => {
        authError.value = null
        showLoginModal.value = true
        showRegisterModal.value = false
    }

    const closeLoginModal = () => {
        showLoginModal.value = false
        authError.value = null
    }

    const openRegisterModal = () => {
        authError.value = null
        showRegisterModal.value = true
        showLoginModal.value = false
    }

    const closeRegisterModal = () => {
        showRegisterModal.value = false
        authError.value = null
    }

    const switchToRegister = () => {
        showLoginModal.value = false
        showRegisterModal.value = true
        authError.value = null
    }

    const switchToLogin = () => {
        showRegisterModal.value = false
        showLoginModal.value = true
        authError.value = null
    }

    return {
        // State
        user,
        isLoggedIn,
        showLoginModal,
        showRegisterModal,
        authLoading,
        authError,
        authSuccessMessage,

        // Actions
        initAuth,
        register,
        login,
        logout,
        fetchUser,

        // Modal controls
        openLoginModal,
        closeLoginModal,
        openRegisterModal,
        closeRegisterModal,
        switchToRegister,
        switchToLogin
    }
}
