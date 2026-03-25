import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

// In-memory token cache to avoid synchronous localStorage reads on every request
let cachedAccessToken = localStorage.getItem('access_token');
let cachedRefreshToken = localStorage.getItem('refresh_token');

export function setTokens(accessToken, refreshToken) {
    cachedAccessToken = accessToken;
    cachedRefreshToken = refreshToken;
    localStorage.setItem('access_token', accessToken);
    localStorage.setItem('refresh_token', refreshToken);
}

export function clearTokens() {
    cachedAccessToken = null;
    cachedRefreshToken = null;
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
}

api.interceptors.request.use((config) => {
    if (cachedAccessToken) {
        config.headers.Authorization = `Bearer ${cachedAccessToken}`;
    }
    return config;
});

// Shared state for token refresh queue
let isRefreshing = false;
let refreshSubscribers = [];

function onRefreshed(accessToken) {
    refreshSubscribers.forEach((callback) => callback(accessToken));
    refreshSubscribers = [];
}

function addRefreshSubscriber(callback) {
    refreshSubscribers.push(callback);
}

api.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;

        if (error.response?.status === 401 && !originalRequest._retry) {
            originalRequest._retry = true;

            if (!cachedRefreshToken) {
                return Promise.reject(error);
            }

            // If already refreshing, queue this request
            if (isRefreshing) {
                return new Promise((resolve) => {
                    addRefreshSubscriber((accessToken) => {
                        originalRequest.headers.Authorization = `Bearer ${accessToken}`;
                        resolve(api(originalRequest));
                    });
                });
            }

            isRefreshing = true;

            try {
                const { data } = await api.post('/auth/refresh', {
                    refresh_token: cachedRefreshToken,
                });

                const newAccessToken = data.data.access_token;
                setTokens(newAccessToken, data.data.refresh_token);

                originalRequest.headers.Authorization = `Bearer ${newAccessToken}`;
                onRefreshed(newAccessToken);

                return api(originalRequest);
            } catch {
                refreshSubscribers = [];
                clearTokens();
                window.location.href = '/login';

                return Promise.reject(error);
            } finally {
                isRefreshing = false;
            }
        }

        return Promise.reject(error);
    },
);

export default api;
