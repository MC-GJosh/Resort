/**
 * Composable for making API calls to the Laravel backend
 */
export const useApi = () => {
    const config = useRuntimeConfig()
    const apiBase = config.public.apiBase

    /**
     * Get the auth token from localStorage
     */
    const getToken = () => {
        if (process.client) {
            return localStorage.getItem('auth_token')
        }
        return null
    }

    /**
     * Make an API request
     */
    const request = async (endpoint, options = {}) => {
        const token = getToken()

        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...options.headers
        }

        if (token) {
            headers['Authorization'] = `Bearer ${token}`
        }

        const url = `${apiBase}${endpoint}`

        try {
            const response = await $fetch(url, {
                ...options,
                headers
            })
            return { data: response, error: null }
        } catch (err) {
            console.error('API Error:', err)
            return {
                data: null,
                error: err.data || { message: err.message || 'An error occurred' }
            }
        }
    }

    /**
     * GET request
     */
    const get = (endpoint, options = {}) => {
        return request(endpoint, { ...options, method: 'GET' })
    }

    /**
     * POST request
     */
    const post = (endpoint, body, options = {}) => {
        return request(endpoint, { ...options, method: 'POST', body })
    }

    /**
     * PUT request
     */
    const put = (endpoint, body, options = {}) => {
        return request(endpoint, { ...options, method: 'PUT', body })
    }

    /**
     * DELETE request
     */
    const del = (endpoint, options = {}) => {
        return request(endpoint, { ...options, method: 'DELETE' })
    }

    return {
        get,
        post,
        put,
        del,
        getToken
    }
}
