import { apiFetch } from './client';
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
    throw new Error(response.message ?? 'Connexion impossible.');
  }

  return response.data;
}
