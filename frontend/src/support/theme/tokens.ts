import React, { createContext, useContext, useMemo, useState } from 'react';
import { Platform } from 'react-native';

// Two palettes: dark and light (light is warmer / less dark)
const darkColors = {
  background: '#121418',
  surface: '#1E2026',
  surfaceMuted: '#181A1F',
  surfaceElevated: '#262A31',
  textPrimary: '#ECE7E1',
  textSecondary: '#C9C2B9',
  textMuted: '#9C948A',
  accent: '#6C7BFF',
  accentStrong: '#5464E6',
  accentSoft: 'rgba(108,123,255,0.14)',
  border: '#2A2E36',
  danger: '#D16963',
  success: '#6CB889',
};

export const colors = darkColors;

const lightColors = {
  background: '#F7F3EE',
  surface: '#FFFFFF',
  surfaceMuted: '#F1ECE6',
  surfaceElevated: '#FFF8F0',
  textPrimary: '#2C241E',
  textSecondary: '#5C524A',
  textMuted: '#8B8178',
  accent: '#5F6CFF',
  accentStrong: '#4A59E0',
  accentSoft: 'rgba(95,108,255,0.12)',
  border: '#E4D9CE',
  danger: '#B95A53',
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

// Use a clean sans stack for a modern, app-like UI (Inter / system fallbacks)
const headingFont =
  Platform.select({
    ios: 'Inter',
    android: 'sans-serif',
    web: '"Inter", -apple-system, system-ui, Roboto, "Helvetica Neue", Arial, sans-serif',
    default: 'System',
  }) ?? 'System';

const uiFont = headingFont;

export const typography = {
  // body / UI font
  fontFamily: uiFont,
  // heading font (serif) used for titles
  headingFont,
  display: 30,
  title: 22,
  body: 15,
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
  theme: typeof themes.dark;
};

const ThemeContext = createContext<ThemeContextValue | undefined>(undefined);

export function ThemeProvider({ children }: { children: React.ReactNode }) {
  const [mode, setMode] = useState<ThemeMode>('dark');
  const toggle = () => setMode((m) => (m === 'dark' ? 'light' : 'dark'));
  const value = useMemo(() => ({ mode, setMode, toggle, theme: themes[mode] }), [mode]);
  return React.createElement(ThemeContext.Provider, { value }, children);
}

export function useTheme() {
  const ctx = useContext(ThemeContext);
  if (!ctx) {
    // fallback to dark theme if provider not present
    return { mode: 'dark', setMode: () => {}, toggle: () => {}, theme: themes.dark } as ThemeContextValue;
  }
  return ctx;
}
