import { useMemo } from 'react';
import { moodOptions } from '../../../../support/theme/moods';
import { useTheme } from '../../../../support/theme/tokens';
import { getMoodLore } from '../utils/moodLore';
import { isDarkColor, mixColor, withAlpha } from '../utils/color';

export type WriteMoodTheme = {
  isNeutral: boolean;
  moodAccent: string;
  primaryColor: string;
  primaryHoverColor: string;
  primaryTextColor: string;
  focusBorderColor: string;
  moodSurfaceStrong: string;
  moodSurfaceLight: string;
  editorFieldBackground: string;
  moodBackdropStrong: string;
  moodBackdropSoft: string;
  moodBackdropRing: string;
  moodTextColor: string;
  moodDescription: string;
};

/**
 * Builds mood-driven colors and copy for the write experience.
 */
export function useWriteMoodTheme(moodKey: string): WriteMoodTheme {
  const { theme } = useTheme();
  const colors = theme.colors;
  return useMemo(() => {
    const activeMood =
      moodOptions.find((option) => option.key === moodKey) ?? moodOptions[0];
    const moodAccent = activeMood.color;
    const isNeutral = moodKey === 'neutre';
    const primaryColor = isNeutral ? colors.accent : moodAccent;
    const moodSurfaceStrong = isNeutral ? colors.surface : withAlpha(moodAccent, '22');
    const moodSurfaceLight = isNeutral
      ? colors.surfaceElevated
      : withAlpha(moodAccent, '12');
    const editorFieldBackground = isNeutral
      ? colors.surfaceElevated
      : withAlpha(moodAccent, '0D');
    const moodBackdropStrong = isNeutral
      ? colors.accentSoft
      : withAlpha(moodAccent, '26');
    const moodBackdropSoft = isNeutral ? colors.surfaceMuted : withAlpha(moodAccent, '12');
    const moodBackdropRing = isNeutral ? colors.border : withAlpha(moodAccent, '33');
    const primaryHoverColor = mixColor(primaryColor, '#FFFFFF', 0.18);
    const focusBorderColor = primaryColor;
    const moodTextWeight = isDarkColor(moodAccent) ? 0.32 : 0.18;
    const moodTextColor = isNeutral
      ? colors.textPrimary
      : mixColor(colors.textPrimary, moodAccent, moodTextWeight);
    const primaryTextColor =
      !isNeutral && isDarkColor(moodAccent) ? colors.surface : colors.textPrimary;
    const moodLore = getMoodLore(moodKey);
    const moodDescription = moodLore?.description ?? 'Perception en attente.';

    return {
      isNeutral,
      moodAccent,
      primaryColor,
      primaryHoverColor,
      primaryTextColor,
      focusBorderColor,
      moodSurfaceStrong,
      moodSurfaceLight,
      editorFieldBackground,
      moodBackdropStrong,
      moodBackdropSoft,
      moodBackdropRing,
      moodTextColor,
      moodDescription,
    };
  }, [colors, moodKey]);
}
