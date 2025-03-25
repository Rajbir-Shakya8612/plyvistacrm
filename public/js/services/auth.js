class AuthService {
    constructor() {
        this.baseURL = '/api';
        this.setupAxiosDefaults();
    }

    setupAxiosDefaults() {
        axios.interceptors.request.use(
            (config) => {
                const token = localStorage.getItem('auth_token');
                if (token) {
                    config.headers.Authorization = `Bearer ${token}`;
                    config.headers.Accept = 'application/json';
                }
                return config;
            },
            (error) => Promise.reject(error)
        );

        axios.interceptors.response.use(
            (response) => response,
            (error) => {
                if (error.response?.status === 401) {
                    this.logout();
                }
                if (error.response?.status === 403) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Access Denied',
                        text: 'You do not have permission to access this resource.',
                        confirmButtonColor: '#3085d6',
                    });
                }
                return Promise.reject(error);
            }
        );
    }

    async login(credentials) {
        try {
            // First try web authentication
            try {
                const response = await axios.post('/login', credentials, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.data.success) {
                    localStorage.setItem('auth_token', response.data.token);
                    localStorage.setItem('user', JSON.stringify(response.data.user));
                    return response.data;
                }
            } catch (webError) {
                // If web auth fails, try API authentication
                const apiResponse = await axios.post(`${this.baseURL}/login`, credentials);
                if (apiResponse.data.success) {
                    localStorage.setItem('auth_token', apiResponse.data.token);
                    localStorage.setItem('user', JSON.stringify(apiResponse.data.user));
                    return apiResponse.data;
                }
            }
            throw new Error('Login failed');
        } catch (error) {
            throw error;
        }
    }

    async logout() {
        try {
            // Try web logout first
            try {
                await axios.post('/logout');
            } catch (webError) {
                // If web logout fails, try API logout
                await axios.post(`${this.baseURL}/logout`);
            }
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
            window.location.href = '/login';
        }
    }

    isAuthenticated() {
        return !!localStorage.getItem('auth_token');
    }

    getUser() {
        const user = localStorage.getItem('user');
        return user ? JSON.parse(user) : null;
    }

    getToken() {
        return localStorage.getItem('auth_token');
    }

    hasRole(role) {
        const user = this.getUser();
        return user && user.role === role;
    }

    async fetchDashboard() {
        try {
            const token = this.getToken();
            if (!token) {
                throw new Error('Token not found');
            }
            const user = this.getUser();
            if (!user) {
                throw new Error('User not authenticated');
            }
            const response = await axios.get(`${this.baseURL}/${user.role}/dashboard`, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: 'application/json',
                },
            });

            console.log("Dashboard Response:", response); // Debugging Response

            if (!response.data.success) {
                throw new Error(response.data.message || 'Failed to fetch dashboard data');
            }
            return response.data;
        } catch (error) {
            console.error('Dashboard fetch error:', error);
            throw error;
        }
    }

}

window.authService = new AuthService();
