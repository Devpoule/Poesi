import React from 'react';
import {
  SafeAreaView,
  ScrollView,
  StyleProp,
  StyleSheet,
  View,
  ViewStyle,
  Platform,
  useWindowDimensions,
} from 'react-native';
import { spacing, useTheme } from '../../support/theme/tokens';

type ScreenProps = {
  children: React.ReactNode;
  style?: StyleProp<ViewStyle>;
  contentStyle?: StyleProp<ViewStyle>;
  scroll?: boolean;
};

export function Screen({ children, style, contentStyle, scroll = false }: ScreenProps) {
  const { theme } = useTheme();
  const colors = theme.colors;
  const { width } = useWindowDimensions();
  const sideInset = width < 720 ? spacing.sm : Math.round(width * 0.06);
  const panelWidth = 260;
  const panelGap = spacing.md;
  const panelInset =
    Platform.OS === 'web' && width >= 1100 ? panelWidth + panelGap : 0;
  const styles = createStyles(colors, sideInset, width, panelInset);
  return (
    <SafeAreaView style={[styles.safeArea, style]}>
      <View style={styles.atmosphere}>
        <View style={styles.pageVeil} />
        <View style={styles.orbTop} />
        <View style={styles.orbBottom} />
        <View style={styles.ring} />
      </View>
      {scroll ? (
        <ScrollView
          contentContainerStyle={[styles.content, contentStyle]}
          showsVerticalScrollIndicator={false}
        >
          {children}
        </ScrollView>
      ) : (
        <View style={[styles.content, contentStyle]}>{children}</View>
      )}
    </SafeAreaView>
  );
}

function createStyles(colors: any, sideInset: number, width: number, panelInset: number) {
  return StyleSheet.create({
    safeArea: {
      flex: 1,
      backgroundColor: colors.background,
      overflow: 'hidden',
    },
    atmosphere: {
      ...StyleSheet.absoluteFillObject,
      pointerEvents: 'none',
    },
    pageVeil: {
      position: 'absolute',
      top: -spacing.lg,
      left: -spacing.lg,
      right: -spacing.lg,
      height: 220,
      borderBottomLeftRadius: 32,
      borderBottomRightRadius: 32,
      opacity: 0.75,
      backgroundColor: colors.surfaceElevated,
      pointerEvents: 'none',
    },
    orbTop: {
      position: 'absolute',
      width: 220,
      height: 220,
      borderRadius: 110,
      backgroundColor: colors.accent,
      top: -90,
      right: -60,
      opacity: 0.6,
    },
    orbBottom: {
      position: 'absolute',
      width: 260,
      height: 260,
      borderRadius: 130,
      backgroundColor: colors.surfaceMuted,
      bottom: -140,
      left: -80,
      opacity: 0.7,
    },
    ring: {
      position: 'absolute',
      width: 180,
      height: 180,
      borderRadius: 90,
      borderWidth: 1,
      borderColor: colors.border,
      top: 120,
      left: -40,
      opacity: 0.35,
    },
    content: {
      flex: 1,
      flexGrow: 1,
      position: 'relative',
      paddingLeft: sideInset + panelInset,
      paddingRight: sideInset + panelInset,
      paddingTop: spacing.lg,
      paddingBottom: spacing.lg,
      ...(Platform.select({
        web: { paddingTop: width < 720 ? spacing.xl : 96 } as ViewStyle,
        default: {} as ViewStyle,
      }) as ViewStyle),
    },
  });
}
