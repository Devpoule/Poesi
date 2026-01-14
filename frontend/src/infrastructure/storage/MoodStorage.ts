import * as SecureStore from 'expo-secure-store';
import { Platform } from 'react-native';

const storageKey = 'poesi.mood';

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

export class MoodStorage {
  async getMood(): Promise<string | null> {
    if (isWeb()) {
      return await getWebItem();
    }
    return await SecureStore.getItemAsync(storageKey);
  }

  async setMood(value: string): Promise<void> {
    if (isWeb()) {
      await setWebItem(value);
      return;
    }
    await SecureStore.setItemAsync(storageKey, value);
  }

  async clear(): Promise<void> {
    if (isWeb()) {
      await removeWebItem();
      return;
    }
    await SecureStore.deleteItemAsync(storageKey);
  }
}
