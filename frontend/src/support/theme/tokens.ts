import React, { createContext, useContext, useMemo, useState } from 'react';
import { Platform } from 'react-native';

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
  theme: typeof themes.dark;
};

const ThemeContext = createContext<ThemeContextValue | undefined>(undefined);

export function ThemeProvider({ children }: { children: React.ReactNode }) {
  const [mode, setMode] = useState<ThemeMode>('light');
  const toggle = () => setMode((m) => (m === 'dark' ? 'light' : 'dark'));
  const value = useMemo(() => ({ mode, setMode, toggle, theme: themes[mode] }), [mode]);
  return React.createElement(ThemeContext.Provider, { value }, children);
}

export function useTheme() {
  const ctx = useContext(ThemeContext);
  if (!ctx) {
    // fallback to dark theme if provider not present
    return { mode: 'light', setMode: () => {}, toggle: () => {}, theme: themes.light } as ThemeContextValue;
  }
  return ctx;
}
