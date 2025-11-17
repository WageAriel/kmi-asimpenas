import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Function to get CSRF token
function getCsrfToken() {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        return token.content;
    }
    
    // Fallback: try to get from cookie
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    if (match) {
        return decodeURIComponent(match[1]);
    }
    
    return null;
}

// Set CSRF token
const token = getCsrfToken();
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
} else {
    console.error('CSRF token not found');
}

// Axios interceptor to refresh CSRF token before each request
window.axios.interceptors.request.use(function (config) {
    const currentToken = getCsrfToken();
    if (currentToken) {
        config.headers['X-CSRF-TOKEN'] = currentToken;
    }
    return config;
}, function (error) {
    return Promise.reject(error);
});

// Axios interceptor to handle CSRF token mismatch errors
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 419) {
            // CSRF token mismatch - reload the page to get new token
            console.error('CSRF token mismatch. Reloading page...');
            window.location.reload();
        }
        return Promise.reject(error);
    }
);
