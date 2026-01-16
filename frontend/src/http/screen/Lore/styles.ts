import { useMemo } from 'react';
import { Platform, StyleSheet, useWindowDimensions } from 'react-native';
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

function createStyles(colors: ThemeColors, width: number) {
  const colCount = width >= 1200 ? 4 : width >= 900 ? 3 : 2; // minimum 2, maximum 4
  const isCompact = width < 720;
  const itemWidth =
    colCount === 4 ? '23%' : colCount === 3 ? '30%' : '46%';
  const itemMaxWidth = colCount === 4 ? 180 : colCount === 3 ? 220 : 260;
  const itemMinWidth = colCount === 4 ? 150 : colCount === 3 ? 180 : 160;
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
      width: isCompact ? '100%' : '47%',
      backgroundColor: colors.surface,
      borderRadius: 20,
      padding: spacing.md,
      borderWidth: 1,
      borderColor: colors.border,
      marginRight: isCompact ? 0 : spacing.sm,
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
      minWidth: isCompact ? '100%' : 180,
      backgroundColor: colors.surface,
      borderRadius: 18,
      padding: spacing.md,
      borderWidth: 1,
      borderColor: colors.border,
      marginRight: isCompact ? 0 : spacing.sm,
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
      flexDirection: 'row',
      flexWrap: 'wrap',
      gap: spacing.sm,
    },
    itemCard: {
      backgroundColor: colors.surface,
      borderRadius: 20,
      padding: spacing.sm,
      borderWidth: 1,
      borderColor: colors.border,
      marginBottom: spacing.sm,
      width: isCompact ? '48%' : itemWidth,
      maxWidth: isCompact ? 260 : itemMaxWidth,
      minWidth: isCompact ? 140 : itemMinWidth,
      marginRight: 0,
      alignSelf: 'flex-start',
    },
    itemCardNarrow: {
      width: isCompact ? '44%' : colCount === 4 ? '22%' : colCount === 3 ? '28%' : '44%',
      maxWidth: isCompact ? 240 : colCount === 4 ? 170 : colCount === 3 ? 210 : 240,
      minWidth: isCompact ? 140 : colCount === 4 ? 140 : colCount === 3 ? 170 : 150,
    },
    itemCardSelected: {
      borderColor: colors.accent,
      backgroundColor: colors.surfaceMuted,
    },
    itemHeader: {
      flexDirection: 'row',
      alignItems: 'center',
      marginBottom: spacing.xs,
    },
    itemImage: {
      width: '60%',
      maxWidth: 220,
      alignSelf: 'center',
      aspectRatio: 0.72,
      marginBottom: spacing.xs,
      ...Platform.select({
        web: {
          width: '52%',
          maxWidth: 180,
        },
        default: {},
      }),
    },
    itemColorCard: {
      borderRadius: 14,
      borderWidth: 1,
      borderColor: colors.border,
      backgroundColor: colors.surfaceMuted,
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
  const { width } = useWindowDimensions();
  return useMemo(() => createStyles(theme.colors, width), [theme.colors, width]);
}
