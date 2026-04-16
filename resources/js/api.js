/**
 * MJF API client — thin fetch wrapper that automatically
 * injects the Bearer token stored in the page's meta tag.
 */

const BASE = '';

function getToken() {
    return document.querySelector('meta[name="api-token"]')?.content ?? '';
}

function headers(extra = {}) {
    return {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${getToken()}`,
        ...extra,
    };
}

async function request(method, path, body = null) {
    const opts = { method, headers: headers() };
    if (body !== null) opts.body = JSON.stringify(body);

    const res = await fetch(`${BASE}${path}`, opts);

    if (res.status === 401) {
        window.location.href = '/login';
        return null;
    }

    const json = await res.json().catch(() => null);

    if (!res.ok) {
        const error = new Error(json?.message ?? 'Request failed');
        error.errors = json?.errors ?? {};
        error.status = res.status;
        throw error;
    }

    return json;
}

export const api = {
    get:    (path)        => request('GET',    path),
    post:   (path, body)  => request('POST',   path, body),
    put:    (path, body)  => request('PUT',    path, body),
    patch:  (path, body)  => request('PATCH',  path, body),
    delete: (path)        => request('DELETE', path),
};
