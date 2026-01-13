import { useMemo } from 'react';
import { Platform, StyleSheet } from 'react-native';
import { ThemeColors, spacing, typography, useTheme } from '../../../support/theme/tokens';

const cardShadowStyle = Platform.select({
  web: { boxShadow: '0px 8px 20px rgba(0,0,0,0.08)' } as any,
  default: {
    shadowColor: '#000000',
    shadowOpacity: 0.08,
    shadowRadius: 16,
    shadowOffset: { width: 0, height: 8 },
    elevation: 3,
  },
}) as any;

function createStyles(colors: ThemeColors) {
  return StyleSheet.create({
    page: {
      width: '100%',
    },
    header: {
      marginBottom: spacing.lg,
    },
    title: {
      fontSize: typography.display,
      fontFamily: typography.headingFont,
      color: colors.textPrimary,
    },
    subtitle: {
      marginTop: spacing.xs,
      fontSize: typography.body,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
    },
    backLink: {
      alignSelf: 'flex-start',
      marginTop: spacing.sm,
      paddingVertical: 4,
      paddingHorizontal: spacing.sm,
      borderRadius: 999,
      borderWidth: 1,
      borderColor: colors.border,
      backgroundColor: colors.surfaceMuted,
    },
    backLinkText: {
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
    },
    grid: {
      flexDirection: 'row',
      flexWrap: 'wrap',
      marginBottom: spacing.lg,
    },
    gridCard: {
      width: '47%',
      backgroundColor: colors.surface,
      borderRadius: 20,
      padding: spacing.md,
      borderWidth: 1,
      borderColor: colors.border,
      marginRight: spacing.sm,
      marginBottom: spacing.sm,
      ...cardShadowStyle,
    },
    gridCardTag: {
      alignSelf: 'flex-start',
      paddingVertical: 2,
      paddingHorizontal: spacing.sm,
      borderRadius: 999,
      backgroundColor: colors.surfaceMuted,
      marginBottom: spacing.sm,
    },
    gridCardTagText: {
      fontSize: typography.small,
      fontFamily: typography.fontFamily,
      color: colors.textMuted,
    },
    gridCardTitle: {
      fontSize: typography.body,
      fontFamily: typography.headingFont,
      color: colors.textPrimary,
      marginBottom: spacing.xs,
    },
    gridCardText: {
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
    },
    infoRow: {
      flexDirection: 'row',
      flexWrap: 'wrap',
      marginBottom: spacing.lg,
    },
    infoCard: {
      flexGrow: 1,
      minWidth: 180,
      backgroundColor: colors.surface,
      borderRadius: 18,
      padding: spacing.md,
      borderWidth: 1,
      borderColor: colors.border,
      marginRight: spacing.sm,
      marginBottom: spacing.sm,
    },
    infoTitle: {
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textMuted,
      textTransform: 'uppercase',
      letterSpacing: 1,
    },
    infoText: {
      marginTop: spacing.xs,
      fontSize: typography.body,
      fontFamily: typography.fontFamily,
      color: colors.textPrimary,
    },
    list: {
      marginBottom: spacing.lg,
    },
    itemCard: {
      backgroundColor: colors.surface,
      borderRadius: 20,
      padding: spacing.md,
      borderWidth: 1,
      borderColor: colors.border,
      marginBottom: spacing.sm,
    },
    itemHeader: {
      flexDirection: 'row',
      alignItems: 'center',
      marginBottom: spacing.xs,
    },
    itemAccent: {
      width: 8,
      height: 8,
      borderRadius: 4,
      marginRight: spacing.sm,
    },
    itemTitle: {
      fontSize: typography.body,
      fontFamily: typography.headingFont,
      color: colors.textPrimary,
      flex: 1,
    },
    itemTag: {
      paddingVertical: 2,
      paddingHorizontal: spacing.sm,
      borderRadius: 999,
      backgroundColor: colors.surfaceMuted,
      marginLeft: spacing.sm,
    },
    itemTagText: {
      fontSize: typography.small,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
    },
    itemText: {
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
    },
    glossarySection: {
      backgroundColor: colors.surface,
      borderRadius: 20,
      padding: spacing.md,
      borderWidth: 1,
      borderColor: colors.border,
      marginBottom: spacing.md,
    },
    glossaryTitle: {
      fontSize: typography.body,
      fontFamily: typography.headingFont,
      color: colors.textPrimary,
      marginBottom: spacing.xs,
    },
    glossaryText: {
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
    },
  });
}

export function useStyles() {
  const { theme } = useTheme();
  return useMemo(() => createStyles(theme.colors), [theme.colors]);
}
