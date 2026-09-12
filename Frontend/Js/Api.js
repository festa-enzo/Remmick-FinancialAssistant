export async function apiFetch(url, options = {}) {
    const token = localStorage.getItem('token');

    if (!token) {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = 'Login.html';
        return null;
    }

    const response = await fetch(url, {
        ...options,
        headers: {
            ...options.headers,
            Authorization: 'Bearer ' + token
        }
    });

    if (response.status === 401) {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = 'Login.html';
        return null;
    }

    if (response.ok) {
    document.documentElement.classList.remove('auth-pending');
    }   
    
    return response;
}