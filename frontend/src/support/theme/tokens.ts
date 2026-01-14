import React, { createContext, useContext, useEffect, useMemo, useState } from 'react';
import { Platform } from 'react-native';
import { moodOptions } from './moods';
import { MoodStorage } from '../../infrastructure/storage/MoodStorage';

// Two palettes: dark and light, warm and paper-like.
const darkColors = {
  background: '#17130F',
  surface: '#221B15',
  surfaceMuted: '#1B1510',
  surfaceElevated: '#2C241D',
  textPrimary: '#F5EEE6',
  textSecondary: '#D7CEC4',
  textMuted: '#B3A79B',
  accent: '#B07A47',
  accentStrong: '#8F5C2B',
  accentSoft: 'rgba(176,122,71,0.22)',
  border: '#2E261F',
  danger: '#C57369',
  success: '#6BA389',
};

export const colors = darkColors;

const lightColors = {
  background: '#F6F1EA',
  surface: '#FFFFFF',
  surfaceMuted: '#F1E8DD',
  surfaceElevated: '#FFF8F0',
  textPrimary: '#2B221B',
  textSecondary: '#5A4F46',
  textMuted: '#8B8178',
  accent: '#B07A47',
  accentStrong: '#8F5C2B',
  accentSoft: 'rgba(176,122,71,0.16)',
  border: '#E6DDD2',
  danger: '#B05F56',
  success: '#4F7D6B',
};

export const themes = {
  dark: { colors: darkColors },
  light: { colors: lightColors },
};
export type ThemeColors = typeof darkColors;
export const spacing = {
  xs: 6,
  sm: 10,
  md: 16,
  lg: 24,
  xl: 32,
  xxl: 40,
};

// Serif for titles, warm sans for UI.
const headingFont =
  Platform.select({
    ios: 'Georgia',
    android: 'serif',
    web: '"Baskerville", "Libre Baskerville", "Georgia", serif',
    default: 'serif',
  }) ?? 'serif';

const uiFont =
  Platform.select({
    ios: 'Avenir Next',
    android: 'sans-serif',
    web: '"Avenir Next", "DM Sans", "Trebuchet MS", sans-serif',
    default: 'System',
  }) ?? 'System';

export const typography = {
  // body / UI font
  fontFamily: uiFont,
  // heading font (serif) used for titles
  headingFont,
  display: 32,
  title: 26,
  body: 16,
  caption: 13,
  small: 12,
};

export const layout = {
  // percentage of the viewport to leave as side margins on web
  sidePercent: '10%',
  // width of the content area on web (complementary to sidePercent)
  contentWidth: '80%',
  // maximum width in px for large screens
  maxWidth: 1200,
};

// Theme context + provider
type ThemeMode = 'light' | 'dark';
type ThemeContextValue = {
  mode: ThemeMode;
  setMode: (m: ThemeMode) => void;
  toggle: () => void;
  toggleMode: () => void;
  theme: typeof themes.dark;
  accentKey: string | null;
  accentColor: string | null;
  setAccentKey: (key: string | null) => void;
};

const ThemeContext = createContext<ThemeContextValue | undefined>(undefined);
const moodStorage = new MoodStorage();

const moodColorByKey = new Map(moodOptions.map((mood) => [mood.key, mood.color]));

function toRgba(color: string, opacity: number) {
  if (!color.startsWith('#') || color.length !== 7) {
    return color;
  }
  const clamped = Math.max(0, Math.min(1, opacity));
  const r = parseInt(color.slice(1, 3), 16);
  const g = parseInt(color.slice(3, 5), 16);
  const b = parseInt(color.slice(5, 7), 16);
  return `rgba(${r}, ${g}, ${b}, ${clamped})`;
}

function mixColor(base: string, mix: string, weight: number) {
  if (!base.startsWith('#') || base.length !== 7) {
    return base;
  }
  if (!mix.startsWith('#') || mix.length !== 7) {
    return base;
  }
  const clamped = Math.max(0, Math.min(1, weight));
  const r = parseInt(base.slice(1, 3), 16);
  const g = parseInt(base.slice(3, 5), 16);
  const b = parseInt(base.slice(5, 7), 16);
  const mr = parseInt(mix.slice(1, 3), 16);
  const mg = parseInt(mix.slice(3, 5), 16);
  const mb = parseInt(mix.slice(5, 7), 16);
  const rr = Math.round(r + (mr - r) * clamped);
  const gg = Math.round(g + (mg - g) * clamped);
  const bb = Math.round(b + (mb - b) * clamped);
  const hex = (value: number) => value.toString(16).padStart(2, '0');
  return `#${hex(rr)}${hex(gg)}${hex(bb)}`;
}

function buildColors(mode: ThemeMode, accentColor: string | null) {
  const base = mode === 'dark' ? darkColors : lightColors;
  if (!accentColor) {
    return base;
  }

  const surfaceTint = mode === 'dark' ? 0.08 : 0.06;
  const elevatedTint = mode === 'dark' ? 0.12 : 0.08;
  const borderTint = mode === 'dark' ? 0.14 : 0.1;
  return {
    ...base,
    background: mixColor(base.background, accentColor, mode === 'dark' ? 0.06 : 0.04),
    surface: mixColor(base.surface, accentColor, surfaceTint),
    surfaceMuted: mixColor(base.surfaceMuted, accentColor, surfaceTint),
    surfaceElevated: mixColor(base.surfaceElevated, accentColor, elevatedTint),
    border: mixColor(base.border, accentColor, borderTint),
    accent: accentColor,
    accentStrong: mixColor(accentColor, base.textPrimary, mode === 'dark' ? 0.18 : 0.12),
    accentSoft: toRgba(accentColor, mode === 'dark' ? 0.28 : 0.18),
  };
}

export function ThemeProvider({ children }: { children: React.ReactNode }) {
  const [mode, setMode] = useState<ThemeMode>('light');
  const [accentKey, setAccentKeyState] = useState<string | null>(null);
  const toggle = () => setMode((m) => (m === 'dark' ? 'light' : 'dark'));
  const toggleMode = toggle;
  const accentColor = useMemo(() => {
    if (!accentKey || accentKey === 'neutre') {
      return null;
    }
    return moodColorByKey.get(accentKey) ?? null;
  }, [accentKey]);

  useEffect(() => {
    let isMounted = true;
    moodStorage.getMood().then((stored) => {
      if (!isMounted) {
        return;
      }
      if (stored && moodColorByKey.has(stored)) {
        setAccentKeyState(stored);
      } else {
        setAccentKeyState(null);
      }
    });
    return () => {
      isMounted = false;
    };
  }, []);

  const setAccentKey = (nextKey: string | null) => {
    setAccentKeyState(nextKey);
    if (nextKey) {
      void moodStorage.setMood(nextKey);
      return;
    }
    void moodStorage.clear();
  };

  const theme = useMemo(() => ({ colors: buildColors(mode, accentColor) }), [mode, accentColor]);
  const value = useMemo(
    () => ({
      mode,
      setMode,
      toggle,
      toggleMode,
      theme,
      accentKey,
      accentColor,
      setAccentKey,
    }),
    [mode, theme, accentKey, accentColor]
  );

  return React.createElement(ThemeContext.Provider, { value }, children);
}

export function useTheme() {
  const ctx = useContext(ThemeContext);
  if (!ctx) {
    // fallback to dark theme if provider not present
    return {
      mode: 'light',
      setMode: () => {},
      toggle: () => {},
      toggleMode: () => {},
      theme: themes.light,
      accentKey: null,
      accentColor: null,
      setAccentKey: () => {},
    } as ThemeContextValue;
  }
  return ctx;
}
