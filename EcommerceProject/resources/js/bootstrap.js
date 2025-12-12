import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

Object.defineProperty(window, 'http', {
    configurable: false,
    enumerable: true,
    value: axios.create({
        baseURL: import.meta.env.VITE_APP_URL,
        allowAbsoluteUrls: true,
        headers: {
            common: {
                'X-Requested-With': 'XMLHttpRequest',
                'Authorization': `Bearer ${window.getCookie('auth_token', localStorage.getItem('auth_token')) ?? ''}`,
                'Content-Type': 'application/json'
            }
        },
        timeout: 10000,
        withCredentials: true,
        responseType: 'json',
        xsrfCookieName: 'XSRF-TOKEN',
        xsrfHeaderName: 'X-XSRF-TOKEN',
        formSerializer: {
            dots: false,
            metaTokens: true,
            indexes: false
        }
    })
});

/* Axios interceptor for automatic token refresh */
window.http.interceptors.response.use(
    response => response,
    async error => {
        let token;
        const requestConfig = error.config;
        const refreshEndpoint = import.meta.env.VITE_APP_URL + '/refresh';

        if(
            requestConfig.url === refreshEndpoint ||
            error.response?.status !== 401 ||
            requestConfig._retry ||
            !(token = window.getCookie('auth_token', localStorage.getItem('auth_token')))
        ) {
            return Promise.reject(error);
        }

        requestConfig._retry = true;

        try {
            const { data } = await window.http.post(refreshEndpoint);

            if(data.success && data.token) {
                const newToken = data.token;
                requestConfig.headers['Authorization'] = `Bearer ${newToken}`;

                if(window.getCookie('auth_token')) {
                    window.setCookie('auth_token', newToken);
                }else {
                    window.localStorage.setItem('auth_token', newToken);
                }

                return window.http(requestConfig);
            }

            return Promise.reject(error);

        }catch(refreshError) {
            return Promise.reject(error);
        }
    },
    { synchronous: false }
);

const temp = window.http.get(import.meta.env.VITE_APP_URL + '/me')
    .then(response => window.secure_userInfo = response.data.user)
    .catch(error => window.secure_userInfo = null);
