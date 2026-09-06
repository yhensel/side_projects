import { resolveApiBaseUrl } from './apiConfig.js';
import { useAuthStore } from '../stores/authStore';

const API_BASE_URL = resolveApiBaseUrl();

let refreshRequest = null;

const refreshAccessToken = async (refreshToken) => {
  const response = await fetch(`${API_BASE_URL}/api/auth/refresh`, {
    method: 'POST',
    headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
    body: JSON.stringify({ refresh_token: refreshToken }),
  });
  const data = await response.json().catch(() => ({}));

  if (!response.ok || !data.token) {
    throw new Error(data?.detail || data?.message || 'Unable to refresh session');
  }

  const nextRefreshToken = data.refresh_token || refreshToken;
  const currentUser = useAuthStore.getState().user;
  useAuthStore.getState().login(currentUser, data.token, nextRefreshToken);
  localStorage.setItem('gloriousmagra.token', data.token);
  localStorage.setItem('gloriousmagra.refreshToken', nextRefreshToken);

  return { token: data.token, refreshToken: nextRefreshToken };
};

const buildHeaders = (token, body) => {
  const headers = {
    Accept: 'application/json',
  };

  if (body instanceof FormData) {
    headers['Content-Type'] = 'multipart/form-data';
  } else {
    headers['Content-Type'] = 'application/json';
  }

  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }

  return headers;
};

const request = async (path, { method = 'GET', body, token, headers = {}, canRefresh = true } = {}) => {
  const payload = body instanceof FormData ? body : body ? JSON.stringify(body) : undefined;
  const response = await fetch(`${API_BASE_URL}${path}`, {
    method,
    headers: {
      ...buildHeaders(token, body),
      ...headers,
    },
    body: payload,
  });

  const data = await response.json().catch(() => ({}));

  if (response.status === 401 && canRefresh && token && path !== '/api/auth/refresh') {
    const refreshToken = localStorage.getItem('gloriousmagra.refreshToken');

    if (refreshToken) {
      refreshRequest ||= refreshAccessToken(refreshToken).finally(() => {
        refreshRequest = null;
      });

      try {
        const refreshed = await refreshRequest;
        return request(path, { method, body, token: refreshed.token, headers, canRefresh: false });
      } catch (refreshError) {
        localStorage.removeItem('gloriousmagra.token');
        localStorage.removeItem('gloriousmagra.refreshToken');
      }
    }
  }

  if (!response.ok) {
    throw new Error(data?.detail || data?.message || 'Request failed');
  }

  return data;
};

export const authApi = {
  login: (credentials) => request('/api/auth/login', { method: 'POST', body: credentials }),
  register: (payload) => request('/api/auth/register', { method: 'POST', body: payload }),
};

export const measurementApi = {
  list: (token) => request('/api/measurements', { token }),
  create: (token, payload) => request('/api/measurements', { method: 'POST', body: payload, token }),
};
