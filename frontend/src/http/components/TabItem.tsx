import React, { useEffect, useMemo, useRef } from 'react';
import { Animated, Platform, StyleSheet, Text, View, useWindowDimensions } from 'react-native';
import { ThemeColors, spacing, typography, useTheme } from '../../support/theme/tokens';

type TabVariant = 'home' | 'poems' | 'write' | 'guide' | 'profile';

type TabTheme = {
  label: string;
  color: string;
  background: string;
};

type TabItemProps = {
  variant: TabVariant;
  focused: boolean;
};

type SigilProps = {
  variant: TabVariant;
  color: string;
  styles: ReturnType<typeof createStyles>;
};

const tabShadowStyle = Platform.select({
  web: { boxShadow: '0px 6px 12px rgba(0, 0, 0, 0.08)' } as any,
  default: {
    shadowColor: '#000000',
    shadowOpacity: 0.08,
    shadowRadius: 12,
    shadowOffset: { width: 0, height: 6 },
    elevation: 3,
  },
}) as any;
const useNativeDriver = Platform.OS !== 'web';

function buildTabThemes(colors: ThemeColors): Record<TabVariant, TabTheme> {
  const palette = {
    color: colors.accentStrong,
    background: colors.accentSoft,
  };
  return {
    home: { label: 'Accueil', ...palette },
    poems: { label: 'Poemes', ...palette },
    write: { label: 'Ecrire', ...palette },
    guide: { label: 'Guide', ...palette },
    profile: { label: 'Profil', ...palette },
  };
}

function Sigil({ variant, color, styles }: SigilProps) {
  if (variant === 'home') {
    return (
      <View style={styles.sigil}>
        <View style={[styles.homeBase, { borderColor: color }]} />
        <View style={[styles.homeRoofLeft, { backgroundColor: color }]} />
        <View style={[styles.homeRoofRight, { backgroundColor: color }]} />
      </View>
    );
  }

  if (variant === 'poems') {
    return (
      <View style={styles.sigil}>
        <View style={[styles.bookLeft, { borderColor: color }]} />
        <View style={[styles.bookRight, { borderColor: color }]} />
        <View style={[styles.bookSpine, { backgroundColor: color }]} />
      </View>
    );
  }

  if (variant === 'write') {
    return (
      <View style={styles.sigil}>
        <View style={[styles.penBody, { backgroundColor: color }]} />
        <View style={[styles.penTip, { backgroundColor: color }]} />
      </View>
    );
  }

  if (variant === 'guide') {
    return (
      <View style={styles.sigil}>
        <View style={[styles.guidePage, { borderColor: color }]} />
        <View style={[styles.guideLine, { backgroundColor: color }]} />
        <View style={[styles.guideLine, styles.guideLineShort, { backgroundColor: color }]} />
      </View>
    );
  }

  return (
    <View style={styles.sigil}>
      <View style={[styles.profileRing, { borderColor: color }]} />
      <View style={[styles.profileDot, { backgroundColor: color }]} />
    </View>
  );
}

export function TabItem({ variant, focused }: TabItemProps) {
  const { theme } = useTheme();
  const { width } = useWindowDimensions();
  const isCompact = width < 480;
  const styles = useMemo(() => createStyles(theme.colors), [theme.colors]);
  const tabThemes = useMemo(() => buildTabThemes(theme.colors), [theme.colors]);
  const itemTheme = tabThemes[variant];
  const labelColor = focused ? itemTheme.color : theme.colors.textMuted;
  const anim = useRef(new Animated.Value(1)).current;

  useEffect(() => {
    const toValue = focused ? 1.03 : 1;
    Animated.timing(anim, { toValue, duration: 200, useNativeDriver }).start();
  }, [focused, anim]);

  const transformStyle: any = Platform.select({
    web: [{ scale: anim }],
    default: [
      {
        translateY: anim.interpolate({ inputRange: [1, 1.03], outputRange: [0, -6] }),
      },
    ],
  });

  return (
    <Animated.View
      accessible
      accessibilityRole="button"
      accessibilityLabel={itemTheme.label}
      style={[
        styles.container,
        {
          borderColor: itemTheme.color,
          backgroundColor: focused ? itemTheme.background : theme.colors.surface,
        },
        styles.containerActive,
        { transform: transformStyle },
        focused && Platform.OS === 'web' ? { zIndex: 1100 } : {},
      ]}
    >
      <Sigil variant={variant} color={itemTheme.color} styles={styles} />
      {isCompact ? null : <Text style={[styles.label, { color: labelColor }]}>{itemTheme.label}</Text>}
    </Animated.View>
  );
}

function createStyles(colors: ThemeColors) {
  return StyleSheet.create({
    container: {
      alignItems: 'center',
      justifyContent: 'center',
      paddingVertical: 6,
      paddingHorizontal: 2,
      minWidth: 30,
      marginHorizontal: 0,
      borderRadius: 14,
      borderWidth: 0,
      backgroundColor: colors.surface,
      ...Platform.select({
        web: {
          paddingVertical: 6,
          paddingHorizontal: spacing.sm,
          minWidth: 80,
          borderRadius: 18,
        },
        default: {},
      }),
    },
    containerActive: {
      ...Platform.select({
        web: {
          transform: [{ scale: 1.03 }],
          zIndex: 1100,
        },
        default: {
          transform: [{ translateY: -2 }],
        },
      }),
      ...tabShadowStyle,
    },
    sigil: {
      width: 16,
      height: 16,
      marginBottom: 2,
      position: 'relative',
      ...Platform.select({
        web: {
          width: 20,
          height: 20,
          marginBottom: 2,
        },
        default: {},
      }),
    },
    label: {
      fontSize: typography.small,
      fontFamily: typography.fontFamily,
      ...Platform.select({
        web: {
          fontSize: 14,
          fontWeight: '600' as any,
          marginTop: 2,
        },
        default: {},
      }),
    },
    homeBase: {
      position: 'absolute',
      width: 12,
      height: 8,
      borderWidth: 1,
      borderRadius: 2,
      bottom: 2,
      left: 5,
    },
    homeRoofLeft: {
      position: 'absolute',
      width: 10,
      height: 2,
      borderRadius: 2,
      top: 6,
      left: 2,
      transform: [{ rotate: '-40deg' }],
    },
    homeRoofRight: {
      position: 'absolute',
      width: 10,
      height: 2,
      borderRadius: 2,
      top: 6,
      right: 2,
      transform: [{ rotate: '40deg' }],
    },
    bookLeft: {
      position: 'absolute',
      width: 7,
      height: 12,
      borderWidth: 1,
      borderRadius: 2,
      left: 2,
      top: 4,
    },
    bookRight: {
      position: 'absolute',
      width: 7,
      height: 12,
      borderWidth: 1,
      borderRadius: 2,
      right: 2,
      top: 4,
    },
    bookSpine: {
      position: 'absolute',
      width: 2,
      height: 12,
      top: 4,
      left: 10,
      borderRadius: 1,
    },
    penBody: {
      position: 'absolute',
      width: 14,
      height: 2,
      top: 9,
      left: 2,
      borderRadius: 2,
      transform: [{ rotate: '-35deg' }],
    },
    penTip: {
      position: 'absolute',
      width: 5,
      height: 5,
      top: 12,
      right: 2,
      borderRadius: 1,
      transform: [{ rotate: '45deg' }],
    },
    guidePage: {
      position: 'absolute',
      width: 14,
      height: 16,
      borderWidth: 1,
      borderRadius: 2,
      top: 2,
      left: 3,
    },
    guideLine: {
      position: 'absolute',
      width: 8,
      height: 2,
      top: 6,
      left: 6,
      borderRadius: 2,
    },
    guideLineShort: {
      top: 10,
      width: 6,
    },
    profileRing: {
      position: 'absolute',
      width: 12,
      height: 12,
      borderRadius: 6,
      borderWidth: 2,
      top: 3,
      left: 5,
    },
    profileDot: {
      position: 'absolute',
      width: 6,
      height: 6,
      borderRadius: 3,
      bottom: 3,
      left: 8,
    },
  });
}
