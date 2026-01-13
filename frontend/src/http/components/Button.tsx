import React from 'react';
import { Platform, Pressable, StyleSheet, Text, ViewStyle } from 'react-native';
import { spacing, typography, useTheme } from '../../support/theme/tokens';

type Variant = 'primary' | 'secondary';

type ButtonProps = {
  title: string;
  onPress?: () => void;
  variant?: Variant;
  disabled?: boolean;
  style?: ViewStyle | ViewStyle[];
  backgroundColor?: string;
  hoverBackgroundColor?: string;
  textColor?: string;
};

const shadowStyle = Platform.select({
  web: { boxShadow: '0px 6px 16px rgba(0,0,0,0.08)' } as any,
  default: {
    shadowColor: '#000',
    shadowOpacity: 0.08,
    shadowRadius: 12,
    shadowOffset: { width: 0, height: 6 },
    elevation: 4,
  },
}) as any;

export function Button({
  title,
  onPress,
  variant = 'primary',
  disabled = false,
  style,
  backgroundColor,
  hoverBackgroundColor,
  textColor,
}: ButtonProps) {
  const { theme } = useTheme();
  const colors = theme.colors;
  const isPrimary = variant === 'primary';
  const bg = backgroundColor ?? (isPrimary ? colors.accent : colors.surface);
  const hoverBg = hoverBackgroundColor ?? (isPrimary ? colors.accentStrong : colors.surfaceElevated);
  const txt = textColor ?? (isPrimary ? '#FFFFFF' : colors.textSecondary);

  const styles = createStyles(colors);
  return (
    <Pressable
      disabled={disabled}
      onPress={onPress}
      style={(state) => {
        const hovered = (state as { hovered?: boolean }).hovered;
        const pressed = state.pressed;
        return [
          styles.base,
          isPrimary ? styles.primary : styles.secondary,
          { backgroundColor: hovered || pressed ? hoverBg : bg },
          hovered && styles.hover,
          pressed && styles.pressed,
          style,
        ];
      }}
    >
      <Text style={[styles.text, { color: txt }]}>{title}</Text>
    </Pressable>
  );
}

function createStyles(colors: any) {
  return StyleSheet.create({
    base: {
      paddingVertical: spacing.sm,
      paddingHorizontal: spacing.lg,
      borderRadius: 999,
      alignItems: 'center',
      justifyContent: 'center',
      borderWidth: 0,
      borderColor: 'transparent',
    },
    primary: {
      borderColor: 'transparent',
      ...shadowStyle,
      ...Platform.select({ web: { transition: 'transform 180ms, box-shadow 180ms' } as any, default: {} }),
    },
    secondary: {
      backgroundColor: colors.surface,
    },
    text: {
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
    },
    hover: {
      transform: [{ translateY: -1 }],
    },
    pressed: {
      opacity: 0.85,
    },
  });
}

export default Button;
