import { useState } from 'react';
import { Platform, Pressable, View } from 'react-native';
import { styles } from '../styles';
import { isDarkColor, mixColor, toRgba } from '../utils/color';

type MoodBadgeProps = {
  label: string;
  color: string;
  active: boolean;
  onPress: () => void;
};

/**
 * Displays a selectable mood chip with depth styling.
 */
export function MoodBadge({ label, color, active, onPress }: MoodBadgeProps) {
  const [isHovered, setIsHovered] = useState(false);
  const darkShade = isDarkColor(color);
  const lightShadow = mixColor(color, '#FFFFFF', darkShade ? 0.35 : 0.6);
  const darkShadow = mixColor(color, '#000000', darkShade ? 0.5 : 0.3);
  const lightShadowRgba = toRgba(lightShadow, 0.25);
  const darkShadowRgba = toRgba(darkShadow, 0.18);
  const lightShadowStyle =
    Platform.OS === 'web'
      ? ({ boxShadow: `-3px -3px 6px ${lightShadowRgba}` } as any)
      : { shadowColor: lightShadow };
  const darkShadowStyle =
    Platform.OS === 'web'
      ? ({ boxShadow: `3px 3px 6px ${darkShadowRgba}` } as any)
      : { shadowColor: darkShadow };

  return (
    <View style={[styles.moodBadge, isHovered && styles.moodBadgeHover]}>
      <View
        style={[
          styles.moodShadow,
          styles.moodShadowLight,
          { backgroundColor: color },
          lightShadowStyle,
        ]}
      />
      <View
        style={[
          styles.moodShadow,
          styles.moodShadowDark,
          { backgroundColor: color },
          darkShadowStyle,
        ]}
      />
      <Pressable
        onPress={onPress}
        onHoverIn={() => setIsHovered(true)}
        onHoverOut={() => setIsHovered(false)}
        style={[styles.moodChip, { backgroundColor: color }, active && styles.moodChipActive]}
        accessibilityLabel={label}
        accessibilityHint="Sélectionner ce mood"
      />
    </View>
  );
}
