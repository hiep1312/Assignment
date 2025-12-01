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
                'Authorization': `Bearer ${localStorage.getItem('auth_token') ?? ''}`,
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

/* 
url
method
headers
params
data
timeout
onUploadProgress
onDownloadProgress
*/