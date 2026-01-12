import { Platform } from 'react-native';

export const colors = {
  // Dark, modern palette inspired by Discord's interface
  background: '#0F1115', // page background
  surface: '#2F3136', // cards / surfaces
  surfaceMuted: '#232428',
  surfaceElevated: '#36393F',
  textPrimary: '#E6E6E6',
  textSecondary: '#B9BBBE',
  textMuted: '#8E9297',
  accent: '#5865F2', // primary accent (Discord purple)
  accentStrong: '#4752C4',
  accentSoft: 'rgba(88,101,242,0.10)',
  border: '#202225',
  danger: '#F04747',
  success: '#57F287',
};

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
  sidePercent: '20%',
  // width of the content area on web (complementary to sidePercent)
  contentWidth: '60%',
  // maximum width in px for large screens
  maxWidth: 1200,
};
