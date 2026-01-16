import React from 'react';
import { StyleProp, StyleSheet, Text, View, ViewStyle } from 'react-native';
import { ThemeColors, spacing, typography, useTheme } from '../../support/theme/tokens';

type PageHeaderProps = {
  title: string;
  subtitle?: string;
  action?: React.ReactNode;
  style?: StyleProp<ViewStyle>;
};

export function PageHeader({ title, subtitle, action, style }: PageHeaderProps) {
  const { theme } = useTheme();
  const styles = React.useMemo(() => createStyles(theme.colors), [theme.colors]);
  return (
    <View style={[styles.container, style]}>
      <View style={styles.texts}>
        <Text style={styles.title}>{title}</Text>
        {subtitle ? <Text style={styles.subtitle}>{subtitle}</Text> : null}
      </View>
      {action ? <View style={styles.action}>{action}</View> : null}
    </View>
  );
}

function createStyles(colors: ThemeColors) {
  return StyleSheet.create({
    container: {
      marginBottom: spacing.lg,
      flexDirection: 'row',
      justifyContent: 'space-between',
      alignItems: 'flex-start',
      gap: spacing.md,
    },
    texts: { flex: 1, gap: spacing.xs },
    title: {
      fontSize: typography.display,
      fontFamily: typography.headingFont,
      color: colors.textPrimary,
    },
    subtitle: {
      fontSize: typography.body,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
    },
    action: {
      alignSelf: 'flex-start',
    },
  });
}

