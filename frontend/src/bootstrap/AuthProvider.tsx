import React, { createContext, useContext, useEffect, useMemo, useState } from 'react';
import type { AuthTokens } from '../domain/auth/model/AuthTokens';
import { login as loginApi } from '../infrastructure/api/auth';
import { SessionStorage } from '../infrastructure/storage/SessionStorage';

const sessionStorage = new SessionStorage();

type AuthContextValue = {
  tokens: AuthTokens | null;
  isLoading: boolean;
  login: (email: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
};

const AuthContext = createContext<AuthContextValue | null>(null);

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [tokens, setTokens] = useState<AuthTokens | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    let isMounted = true;

    async function loadSession() {
      const stored = await sessionStorage.getTokens();
      if (isMounted) {
        setTokens(stored);
        setIsLoading(false);
      }
    }

    loadSession();

    return () => {
      isMounted = false;
    };
  }, []);

  const value = useMemo<AuthContextValue>(
    () => ({
      tokens,
      isLoading,
      login: async (email, password) => {
        const nextTokens = await loginApi({ email, password });
        await sessionStorage.setTokens(nextTokens);
        setTokens(nextTokens);
      },
      logout: async () => {
        await sessionStorage.clear();
        setTokens(null);
      },
    }),
    [tokens, isLoading]
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth(): AuthContextValue {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within AuthProvider.');
  }
  return context;
}
