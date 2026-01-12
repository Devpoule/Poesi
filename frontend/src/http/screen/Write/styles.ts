import { Platform, StyleSheet } from 'react-native';
import { colors, spacing, typography } from '../../../support/theme/tokens';

const moodShadowLightStyle = Platform.select({
  web: {},
  default: {
    shadowOffset: { width: -3, height: -3 },
    shadowRadius: 6,
    shadowOpacity: 0.25,
  },
}) as any;

const moodShadowDarkStyle = Platform.select({
  web: {},
  default: {
    shadowOffset: { width: 3, height: 3 },
    shadowRadius: 6,
    shadowOpacity: 0.18,
    elevation: 1,
  },
}) as any;

const tooltipShadowStyle = Platform.select({
  web: { boxShadow: '0px 4px 6px rgba(0, 0, 0, 0.08)' } as any,
  default: {
    shadowColor: '#000000',
    shadowOpacity: 0.08,
    shadowRadius: 6,
    shadowOffset: { width: 0, height: 4 },
    elevation: 2,
  },
}) as any;

const hoverTransitionStyle = Platform.select({
  web: {
    transitionProperty: 'background-color,border-color,color,box-shadow',
    transitionDuration: '420ms',
    transitionTimingFunction: 'ease-in-out',
  } as any,
  default: {} as any,
});

const fieldTransitionStyle = Platform.select({
  web: {
    transitionProperty: 'border-color,border-bottom-color,background-color,color',
    transitionDuration: '320ms',
    transitionTimingFunction: 'ease-in-out',
  } as any,
  default: {} as any,
});

const fieldWebStyle = Platform.select({
  web: {
    outlineStyle: 'none',
    outlineWidth: 0,
    outlineColor: 'transparent',
    boxShadow: 'none',
  } as any,
  default: {} as any,
});

export const styles = StyleSheet.create({
  page: {
    width: '100%',
    ...Platform.select({
      web: {
        width: '60%',
        maxWidth: 1200,
        alignSelf: 'center',
      },
      default: {},
    }),
  },
  header: {
    marginBottom: spacing.md,
  },
  headerRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  title: {
    fontSize: typography.display,
    fontFamily: typography.fontFamily,
    color: colors.textPrimary,
  },
  subtitle: {
    marginTop: spacing.xs,
    fontSize: typography.body,
    fontFamily: typography.fontFamily,
    color: colors.textSecondary,
  },
  card: {
    backgroundColor: colors.surface,
    borderRadius: 20,
    padding: spacing.md,
    borderWidth: 1,
    borderColor: colors.border,
    marginBottom: spacing.md,
  },
  editorCard: {
    paddingTop: spacing.md,
    ...hoverTransitionStyle,
  },
  moodPanel: {
    alignItems: 'center',
    ...hoverTransitionStyle,
  },
  moodDescription: {
    fontSize: typography.body,
    fontFamily: typography.fontFamily,
    color: colors.textSecondary,
    marginTop: spacing.sm,
    textAlign: 'center',
  },
  sectionTitle: {
    fontSize: typography.body,
    fontFamily: typography.fontFamily,
    color: colors.textPrimary,
  },
  input: {
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
    paddingVertical: spacing.sm,
    marginBottom: spacing.md,
    fontSize: typography.body,
    color: colors.textPrimary,
    fontFamily: typography.fontFamily,
    ...fieldWebStyle,
    ...fieldTransitionStyle,
  },
  textArea: {
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 16,
    padding: spacing.md,
    minHeight: 180,
    fontSize: typography.body,
    color: colors.textPrimary,
    fontFamily: typography.fontFamily,
    backgroundColor: colors.surfaceElevated,
    ...fieldWebStyle,
    ...fieldTransitionStyle,
  },
  hint: {
    marginTop: spacing.xs,
    marginBottom: spacing.sm,
    fontSize: typography.caption,
    fontFamily: typography.fontFamily,
    color: colors.textSecondary,
  },
  moodGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'center',
    width: '100%',
    marginTop: spacing.sm,
    marginBottom: spacing.sm,
  },
  moodBadge: {
    position: 'relative',
    marginHorizontal: spacing.sm,
    marginBottom: spacing.md,
  },
  moodBadgeHover: {
    transform: [{ scale: 1.05 }],
  },
  moodShadow: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    borderRadius: 15,
    pointerEvents: 'none',
  },
  moodShadowLight: {
    ...moodShadowLightStyle,
  },
  moodShadowDark: {
    ...moodShadowDarkStyle,
  },
  moodChip: {
    width: 30,
    height: 30,
    borderRadius: 15,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.surface,
  },
  moodChipActive: {
    transform: [{ scale: 1.03 }],
  },
  actionRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'flex-start',
    marginTop: spacing.md,
  },
  buttonHover: {
    transform: [{ translateY: -1 }],
  },
  primaryButton: {
    flex: 1,
    paddingVertical: spacing.sm,
    backgroundColor: colors.accent,
    borderRadius: 999,
    alignItems: 'center',
    marginLeft: spacing.md,
    ...hoverTransitionStyle,
  },
  primaryButtonHover: {
    opacity: 0.95,
  },
  primaryButtonText: {
    fontSize: typography.caption,
    color: colors.textPrimary,
    fontFamily: typography.fontFamily,
  },
  secondaryButton: {
    flex: 1,
    borderWidth: 1,
    borderColor: colors.border,
    paddingVertical: spacing.sm,
    borderRadius: 999,
    alignItems: 'center',
    ...hoverTransitionStyle,
  },
  secondaryButtonHover: {
    backgroundColor: colors.surfaceElevated,
  },
  secondaryButtonText: {
    fontSize: typography.caption,
    color: colors.textSecondary,
    fontFamily: typography.fontFamily,
  },
  gateActions: {
    marginTop: spacing.md,
  },
  moodBackdrop: {
    ...StyleSheet.absoluteFillObject,
    pointerEvents: 'none',
  },
  moodVeil: {
    position: 'absolute',
    top: -spacing.lg,
    left: -spacing.lg,
    right: -spacing.lg,
    height: 220,
    borderBottomLeftRadius: 32,
    borderBottomRightRadius: 32,
    opacity: 0.75,
    ...hoverTransitionStyle,
  },
  moodOrbPrimary: {
    position: 'absolute',
    width: 200,
    height: 200,
    borderRadius: 100,
    top: 40,
    right: -90,
    opacity: 0.35,
    ...hoverTransitionStyle,
  },
  moodOrbSecondary: {
    position: 'absolute',
    width: 160,
    height: 160,
    borderRadius: 80,
    bottom: 80,
    left: -70,
    opacity: 0.25,
    ...hoverTransitionStyle,
  },
  moodRing: {
    position: 'absolute',
    width: 140,
    height: 140,
    borderRadius: 70,
    borderWidth: 1,
    top: 130,
    right: 30,
    opacity: 0.3,
    ...hoverTransitionStyle,
  },
  saveIndicator: {
    position: 'relative',
  },
  saveButton: {
    width: 34,
    height: 34,
    borderRadius: 12,
    borderWidth: 1,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.surface,
    ...hoverTransitionStyle,
  },
  saveButtonDisabled: {
    backgroundColor: colors.surfaceElevated,
  },
  saveButtonPressed: {
    opacity: 0.7,
  },
  saveIcon: {
    width: 14,
    height: 14,
    borderWidth: 1,
    borderRadius: 2,
    backgroundColor: colors.surface,
    position: 'relative',
    overflow: 'hidden',
  },
  saveTop: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    height: 4,
  },
  saveBottom: {
    position: 'absolute',
    bottom: 2,
    left: 3,
    width: 8,
    height: 4,
    borderWidth: 1,
    borderRadius: 1,
    backgroundColor: colors.surface,
  },
  saveSlash: {
    position: 'absolute',
    width: 18,
    height: 2,
    borderRadius: 2,
    top: 6,
    left: -2,
    transform: [{ rotate: '-35deg' }],
  },
  saveTooltip: {
    position: 'absolute',
    top: -44,
    left: -40,
    right: -40,
    alignItems: 'center',
    zIndex: 2,
  },
  saveTooltipText: {
    fontSize: typography.small,
    fontFamily: typography.fontFamily,
    color: colors.textSecondary,
    backgroundColor: colors.surface,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: colors.border,
    paddingVertical: 4,
    paddingHorizontal: spacing.sm,
    textAlign: 'center',
    ...tooltipShadowStyle,
  },
  gatePrimaryButton: {
    backgroundColor: colors.accent,
    paddingVertical: spacing.sm,
    borderRadius: 999,
    alignItems: 'center',
  },
  gatePrimaryText: {
    fontSize: typography.caption,
    fontFamily: typography.fontFamily,
    color: colors.textPrimary,
  },
  gateSecondaryButton: {
    marginTop: spacing.sm,
    borderWidth: 1,
    borderColor: colors.border,
    paddingVertical: spacing.sm,
    borderRadius: 999,
    alignItems: 'center',
  },
  gateSecondaryText: {
    fontSize: typography.caption,
    fontFamily: typography.fontFamily,
    color: colors.textSecondary,
  },
  previewCard: {
    backgroundColor: colors.surfaceElevated,
    borderRadius: 20,
    padding: spacing.md,
    borderWidth: 1,
    borderColor: colors.border,
  },
  previewTitle: {
    fontSize: typography.caption,
    fontFamily: typography.fontFamily,
    color: colors.textMuted,
    textTransform: 'uppercase',
    letterSpacing: 1,
  },
  previewText: {
    marginTop: spacing.xs,
    fontSize: typography.body,
    fontFamily: typography.fontFamily,
    color: colors.textSecondary,
  },
});
