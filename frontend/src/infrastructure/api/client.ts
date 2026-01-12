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
  const tokens = await sessionStorage.getTokens();
  const authHeader = tokens?.token ? { Authorization: `Bearer ${tokens.token}` } : {};
  const initHeaders = normalizeHeaders(init?.headers);

  const response = await fetch(`${config.baseUrl}${path}`, {
    headers: {
      'Content-Type': 'application/json',
      ...authHeader,
      ...initHeaders,
    },
    ...init,
  });

  if (!response.ok) {
    throw new Error(`API error: ${response.status}`);
  }

  return (await response.json()) as T;
}
