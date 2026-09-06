import test from 'node:test';
import assert from 'node:assert/strict';
import { resolveApiBaseUrl } from './apiConfig.js';

test('uses the configured API base URL when provided', () => {
  assert.equal(resolveApiBaseUrl({ VITE_API_BASE_URL: 'https://api.example.com' }, {}), 'https://api.example.com');
});

test('falls back to the current host with port 8080 for local network access', () => {
  assert.equal(
    resolveApiBaseUrl({}, { protocol: 'http:', hostname: '192.168.1.42' }),
    'http://192.168.1.42:8080'
  );
});
