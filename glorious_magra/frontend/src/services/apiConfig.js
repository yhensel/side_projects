export const resolveApiBaseUrl = (env = import.meta.env, location = window.location) => {
  const configured = env?.VITE_API_BASE_URL?.trim();

  if (configured) {
    return configured;
  }

  if (!location) {
    return 'http://localhost:8080';
  }

  const { protocol, hostname } = location;

  if (hostname === 'localhost' || hostname === '127.0.0.1' || hostname === '::1') {
    return 'http://localhost:8080';
  }

  return `${protocol}//${hostname}:8080`;
};
