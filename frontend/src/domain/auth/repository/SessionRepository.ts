import type { AuthTokens } from '../model/AuthTokens';

export type SessionRepository = {
  getTokens: () => Promise<AuthTokens | null>;
  setTokens: (tokens: AuthTokens) => Promise<void>;
  clear: () => Promise<void>;
};
