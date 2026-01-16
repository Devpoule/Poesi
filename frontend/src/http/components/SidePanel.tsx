import React from 'react';
import { Platform, StyleProp, StyleSheet, View, ViewStyle } from 'react-native';
import { ThemeColors, spacing, useTheme } from '../../support/theme/tokens';

type SidePanelProps = {
  children: React.ReactNode;
  style?: StyleProp<ViewStyle>;
  width?: number;
};

export function SidePanel({ children, style, width = 260 }: SidePanelProps) {
  const { theme } = useTheme();
  const styles = React.useMemo(() => createStyles(theme.colors, width), [theme.colors, width]);
  return <View style={[styles.container, style]}>{children}</View>;
}

function createStyles(colors: ThemeColors, width: number) {
  return StyleSheet.create({
    container: {
      width,
      maxWidth: '26vw',
      backgroundColor: colors.surface,
      borderRadius: 16,
      padding: spacing.sm,
      borderWidth: 1,
      borderColor: colors.border,
      ...Platform.select({
        web: { boxShadow: '0px 12px 30px rgba(0,0,0,0.15)' } as any,
        default: {
          shadowColor: '#000',
          shadowOpacity: 0.12,
          shadowRadius: 10,
          shadowOffset: { width: 0, height: 6 },
          elevation: 6,
        },
      }),
    },
  });
}

