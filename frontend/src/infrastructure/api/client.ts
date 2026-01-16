import { config } from '../../support/config/env';
import { SessionStorage } from '../storage/SessionStorage';
import type { ApiResponse } from '../../support/http/apiResponse';

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

export class ApiError extends Error {
  public readonly status: number;
  public readonly errors: Record<string, string[]> | null;

  constructor(message: string, status: number, errors: Record<string, string[]> | null = null) {
    super(message);
    this.status = status;
    this.errors = errors;
  }
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

  let response: Response;

  try {
    response = await fetch(`${config.baseUrl}${path}`, {
      headers,
      ...init,
    });
  } catch {
    throw new ApiError("Impossible de contacter le serveur. Vérifie ta connexion.", 0, null);
  }

  let json: ApiResponse<T> | T | null = null;
  try {
    json = (await response.json()) as ApiResponse<T> | T;
  } catch {
    // Ignore JSON parse errors; handled by status check.
  }

  if (!response.ok) {
    const apiResponse = json as ApiResponse<T> | null;
    const errors = apiResponse?.errors ?? null;

    const emailErrors = errors?.email ?? [];
    const hasEmailConflict = emailErrors.some((msg) =>
      /already (used|exists)/i.test(msg ?? '')
    );

    const message =
      (hasEmailConflict && 'Cet email est déjà utilisé.') ||
      apiResponse?.message ||
      (response.status === 401
        ? 'Identifiants invalides.'
        : response.status === 400
        ? 'Données invalides.'
        : response.status === 409
        ? 'Conflit : la donnée existe déjà.'
        : `Erreur serveur (${response.status}). Merci de réessayer.`);

    throw new ApiError(message, response.status, errors);
  }

  return json as T;
}
