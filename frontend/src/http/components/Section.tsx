import React from 'react';
import { StyleProp, StyleSheet, View, ViewStyle } from 'react-native';
import { ThemeColors, spacing, useTheme } from '../../support/theme/tokens';

type SectionProps = {
  children: React.ReactNode;
  style?: StyleProp<ViewStyle>;
  padded?: boolean;
};

export function Section({ children, style, padded = true }: SectionProps) {
  const { theme } = useTheme();
  const styles = React.useMemo(() => createStyles(theme.colors, padded), [theme.colors, padded]);
  return <View style={[styles.container, style]}>{children}</View>;
}

function createStyles(colors: ThemeColors, padded: boolean) {
  return StyleSheet.create({
    container: {
      backgroundColor: colors.surface,
      borderRadius: 16,
      borderWidth: 1,
      borderColor: colors.border,
      padding: padded ? spacing.md : 0,
    },
  });
}

