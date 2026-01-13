import { config } from '../../support/config/env';
import { SessionStorage } from '../storage/SessionStorage';

const sessionStorage = new SessionStorage();

function normalizeHeaders(headers?: HeadersInit): Record<string, string> {
  if (!headers) {
    return {};
  }
  if (headers instanceof Headers) {
    return Object.fromEntries(headers.entries());
  }
  if (Array.isArray(headers)) {
    return Object.fromEntries(headers);
  }
  return headers;
}

export async function apiFetch<T>(path: string, init?: RequestInit): Promise<T> {
  const method = init?.method?.toUpperCase() ?? 'GET';
  const pathname = path.split('?')[0] ?? path;
  const isPublicGet =
    method === 'GET' &&
    (pathname === '/api/poems' ||
      pathname === '/api/poems/full' ||
      pathname.startsWith('/api/feather-votes/poem/'));
  const tokens = await sessionStorage.getTokens();
  const authHeader: Record<string, string> = tokens?.token
    ? { Authorization: `Bearer ${tokens.token}` }
    : {};
  const initHeaders = normalizeHeaders(init?.headers);
  const headers: Record<string, string> = {
    'Content-Type': 'application/json',
    ...(isPublicGet ? {} : authHeader),
    ...initHeaders,
  };

  const response = await fetch(`${config.baseUrl}${path}`, {
    headers,
    ...init,
  });

  if (!response.ok) {
    throw new Error(`API error: ${response.status}`);
  }

  return (await response.json()) as T;
}
