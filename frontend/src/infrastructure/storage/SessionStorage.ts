import * as SecureStore from 'expo-secure-store';
import { Platform } from 'react-native';
import type { AuthTokens } from '../../domain/auth/model/AuthTokens';
import type { SessionRepository } from '../../domain/auth/repository/SessionRepository';

const storageKey = 'poesi.session';

function isWeb(): boolean {
  return Platform.OS === 'web';
}

async function getWebItem(): Promise<string | null> {
  if (typeof window === 'undefined') {
    return null;
  }
  return window.localStorage.getItem(storageKey);
}

async function setWebItem(value: string): Promise<void> {
  if (typeof window === 'undefined') {
    return;
  }
  window.localStorage.setItem(storageKey, value);
}

async function removeWebItem(): Promise<void> {
  if (typeof window === 'undefined') {
    return;
  }
  window.localStorage.removeItem(storageKey);
}

export class SessionStorage implements SessionRepository {
  async getTokens(): Promise<AuthTokens | null> {
    const raw = isWeb()
      ? await getWebItem()
      : await SecureStore.getItemAsync(storageKey);

    if (!raw) {
      return null;
    }

    try {
      return JSON.parse(raw) as AuthTokens;
    } catch {
      return null;
    }
  }

  async setTokens(tokens: AuthTokens): Promise<void> {
    const raw = JSON.stringify(tokens);
    if (isWeb()) {
      await setWebItem(raw);
      return;
    }
    await SecureStore.setItemAsync(storageKey, raw);
  }

  async clear(): Promise<void> {
    if (isWeb()) {
      await removeWebItem();
      return;
    }
    await SecureStore.deleteItemAsync(storageKey);
  }
}
