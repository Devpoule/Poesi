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

export const typography = {
  fontFamily: baseFont,
  display: 32,
  title: 26,
  body: 16,
  caption: 13,
  small: 12,
};
