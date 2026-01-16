import { apiFetch, ApiError } from './client';
import type { AuthTokens } from '../../domain/auth/model/AuthTokens';
import type { ApiResponse } from '../../support/http/apiResponse';

export type LoginPayload = {
  email: string;
  password: string;
};

type LoginResponse = AuthTokens | ApiResponse<AuthTokens>;

export async function login(payload: LoginPayload): Promise<AuthTokens> {
  const response = await apiFetch<LoginResponse>('/api/login_check', {
    method: 'POST',
    body: JSON.stringify(payload),
  });

  if ('token' in response) {
    return response;
  }

  if (!response.status || !response.data) {
    throw new ApiError(response.message ?? 'Connexion impossible.', 400, response.errors ?? null);
  }

  return response.data;
}

export type RegisterPayload = {
  email: string;
  password: string;
  pseudo?: string | null;
};

type RegisterResponse = ApiResponse<{
  id: number;
  email: string;
  pseudo: string | null;
}>;

export async function register(payload: RegisterPayload): Promise<void> {
  const response = await apiFetch<RegisterResponse>('/api/users', {
    method: 'POST',
    body: JSON.stringify({
      email: payload.email,
      password: payload.password,
      pseudo: payload.pseudo ?? null,
      roles: ['ROLE_USER'],
    }),
  });

  if (!response.status) {
    throw new ApiError(response.message ?? 'Inscription impossible.', 400, response.errors ?? null);
  }
}
