const apps = window.location.origin;

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('service-worker.js')
        .then(function (registration) {
            console.log('Service Worker registered with scope:', registration.scope);
        })
        .catch(function (error) {
            console.error('Service Worker registration failed:', error);
        });
}

// Function to make API requests
function apiRequest(method, endpoint, data = null) {
    const url = `${apps}/api/${endpoint}`;

    return new Promise((resolve, reject) => {
        axios({
                method,
                url,
                data
            })
            .then(response => resolve(response.data))
            .catch(error => reject(new Error(error.response.data)));
    });
}

// API object
const api = {
    get: endpoint => apiRequest('GET', endpoint),
    post: (endpoint, data) => apiRequest('POST', endpoint, data),
    put: (endpoint, data) => apiRequest('PUT', endpoint, data),
    delete: endpoint => apiRequest('DELETE', endpoint)
};