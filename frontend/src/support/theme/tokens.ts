import { Platform } from 'react-native';

export const colors = {
  background: '#F6F1EA',
  surface: '#FFFFFF',
  surfaceMuted: '#F1E8DD',
  surfaceElevated: '#FBF7F1',
  textPrimary: '#2B221B',
  textSecondary: '#5A4F46',
  textMuted: '#8A8077',
  accent: '#C9A87A',
  accentStrong: '#8F5C2B',
  accent: '#B07A47',
  accentSoft: '#EAD7BF',
  border: '#E6DDD2',
  danger: '#B74B47',
  success: '#3E6C5A',
};

export const spacing = {
  xs: 6,
  sm: 10,
  md: 16,
  lg: 24,
  xl: 32,
  xxl: 40,
};

const baseFont =
  Platform.select({
    ios: 'Baskerville',
    android: 'serif',
    web: '"Baskerville", "Georgia", "Times New Roman", serif',
    default: 'Georgia',
  }) ?? 'Georgia';

// UI and heading font stacks. Keep serif for headings, sans-serif for UI/body.
const headingFont =
  Platform.select({
    ios: 'Baskerville',
    android: 'serif',
    web: '"Baskerville", "Georgia", "Times New Roman", serif',
    default: 'Georgia',
  }) ?? 'Georgia';

const uiFont =
  Platform.select({
    ios: 'System',
    android: 'sans-serif',
    web: '"Inter", -apple-system, system-ui, Roboto, "Helvetica Neue", Arial, sans-serif',
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
  sidePercent: '20%',
  // width of the content area on web (complementary to sidePercent)
  contentWidth: '60%',
  // maximum width in px for large screens
  maxWidth: 1200,
};
