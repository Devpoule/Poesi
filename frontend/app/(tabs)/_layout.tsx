import { useEffect, useMemo, useState } from 'react';
import { Tabs, usePathname } from 'expo-router';
import {
  Platform,
  Pressable,
  StyleSheet,
  Text,
  View,
  useWindowDimensions,
} from 'react-native';
import { TabItem } from '../../src/http/components/TabItem';
import { ThemeColors, spacing, useTheme } from '../../src/support/theme/tokens';
import { HomeMoodSection } from '../../src/http/screen/Home/components/HomeMoodSection';

export default function TabsLayout() {
  const pathname = usePathname();

  useEffect(() => {
    if (typeof document === 'undefined') return;
    const active = document.activeElement as HTMLElement | null;
    if (active && typeof active.blur === 'function') {
      active.blur();
    }
  }, [pathname]);

  return (
    <>
      <InnerTabs />
      <GlobalMoodPanel />
    </>
  );
}

function InnerTabs() {
  const { theme } = useTheme();
  const colors = theme.colors;
  const { width } = useWindowDimensions();
  const isCompact = width < 720;
  const isWeb = Platform.OS === 'web';
  const panelWidth = 260;
  const panelGap = spacing.md;
  const panelInset = isWeb && width >= 1100 ? panelWidth + panelGap : 0;
  const sideInset = isCompact ? spacing.md : Math.round(width * 0.08) + panelInset;
  return (
    <Tabs
      screenOptions={{
        headerShown: false,
        tabBarShowLabel: false,
        tabBarStyle: {
          backgroundColor: colors.surfaceElevated,
          borderTopColor: 'transparent',
          height: Platform.select({ web: 64, default: 68 }) as number,
          paddingHorizontal: Platform.select({ web: spacing.md, default: 0 }) as number,
          paddingTop: spacing.xs,
          paddingBottom: spacing.xs,
          overflow: 'visible',
          position: isWeb ? 'fixed' : 'absolute',
          left: isWeb ? sideInset : 0,
          right: isWeb ? sideInset : 0,
          top: isWeb && !isCompact ? 14 : undefined,
          bottom: isWeb ? (isCompact ? 14 : undefined) : 0,
          borderRadius: isWeb ? 24 : 0,
          zIndex: Platform.select({ web: 1000, default: undefined }) as any,
          ...Platform.select({
            web: { boxShadow: '0px 12px 30px rgba(0,0,0,0.25)' } as any,
            default: {
              shadowColor: '#000',
              shadowOpacity: 0.12,
              shadowRadius: 10,
              shadowOffset: { width: 0, height: -4 },
              elevation: 6,
            },
          }),
        },
        tabBarItemStyle: Platform.select({
          web: { marginHorizontal: spacing.xs / 2 } as any,
          default: { flex: 1, marginHorizontal: 0, paddingHorizontal: 0 },
        }),
        tabBarHideOnKeyboard: true,
      }}
      initialRouteName="home"
    >
      <Tabs.Screen
        name="home"
        options={{
          title: 'Accueil',
          tabBarIcon: ({ focused }) => <TabItem variant="home" focused={focused} />,
        }}
      />
      <Tabs.Screen
        name="poems"
        options={{
          title: 'Poemes',
          tabBarIcon: ({ focused }) => <TabItem variant="poems" focused={focused} />,
        }}
      />
      <Tabs.Screen
        name="write"
        options={{
          title: 'Ecrire',
          tabBarIcon: ({ focused }) => <TabItem variant="write" focused={focused} />,
        }}
      />
      <Tabs.Screen
        name="guide"
        options={{
          title: 'Guide',
          tabBarIcon: ({ focused }) => <TabItem variant="guide" focused={focused} />,
        }}
      />
      <Tabs.Screen
        name="profile"
        options={{
          title: 'Profil',
          tabBarIcon: ({ focused }) => <TabItem variant="profile" focused={focused} />,
        }}
      />
    </Tabs>
  );
}

function GlobalMoodPanel() {
  const { theme, accentKey, setAccentKey, toggle, mode } = useTheme();
  const styles = useMemo(() => createPaletteStyles(theme.colors), [theme.colors]);
  const { width } = useWindowDimensions();
  const isCompact = width < 720;
  const panelWidth = 260;
  const isWeb = Platform.OS === 'web';
  const isWide = isWeb && width >= 1100;
  const activeKey = accentKey ?? 'neutre';
  const panelRight = spacing.md;
  const panelTop = isWide ? 180 : 18;
  const [isOpen, setIsOpen] = useState(false);
  const open = isWide ? true : isOpen;
  const toggleOpen = () => setIsOpen((prev) => !prev);

  return (
    <>
      {!isWide ? (
        <Pressable
          accessibilityLabel="Ouvrir la palette mood"
          onPress={toggleOpen}
          style={styles.topButton}
        >
          <View style={[styles.fabDot, { backgroundColor: theme.colors.accent }]} />
        </Pressable>
      ) : null}
      {open ? (
        <View
          style={
            Platform.select({
              web: {
                position: 'fixed',
                right: panelRight,
                top: panelTop,
                zIndex: 2000,
                width: panelWidth,
                maxWidth: '92vw',
              } as any,
              default: {
                position: 'absolute',
                right: 16,
                top: 18,
                zIndex: 2000,
                width: '92vw',
              },
            }) as any
          }
        >
          {!isWide ? (
            <Pressable
              style={styles.overlay}
              onPress={() => setIsOpen(false)}
              accessible={false}
            />
          ) : null}
          <View
            style={[
              styles.panel,
              isCompact && styles.panelCompact,
              isWide && { width: panelWidth },
              !isWide && styles.panelMobile,
            ]}
          >
            {!isWide ? (
              <Pressable
                style={styles.closeButton}
                onPress={() => setIsOpen(false)}
                accessibilityLabel="Fermer la palette mood"
              >
                <View style={styles.closeIcon}>
                  <View style={styles.closeBar} />
                  <View style={[styles.closeBar, { transform: [{ rotate: '90deg' }] }]} />
                </View>
              </Pressable>
            ) : null}
            <HomeMoodSection
              selectedKey={activeKey}
              onSelect={(key) => {
                setAccentKey(key === 'neutre' ? null : key);
                if (!isWide) setIsOpen(false);
              }}
              title="Mood"
              hint="Ambiance"
              columns={isWeb ? 2 : 1}
            />
            <Pressable style={styles.modeButton} onPress={toggle}>
              <Text style={styles.modeButtonText}>
                {mode === 'dark' ? 'Mode clair' : 'Mode sombre'}
              </Text>
            </Pressable>
          </View>
        </View>
      ) : null}
    </>
  );
}

function createPaletteStyles(colors: ThemeColors) {
  return StyleSheet.create({
    panel: {
      backgroundColor: colors.surface,
      borderRadius: 16,
      padding: spacing.sm,
      borderWidth: 1,
      borderColor: colors.border,
      minWidth: 260,
      ...Platform.select({
        web: { boxShadow: '0px 10px 24px rgba(0,0,0,0.18)' } as any,
        default: {
          shadowColor: '#000',
          shadowOpacity: 0.18,
          shadowRadius: 14,
          shadowOffset: { width: 0, height: 8 },
          elevation: 8,
        },
      }),
    },
    panelCompact: {
      padding: spacing.xs,
    },
    panelMobile: {
      width: '92vw',
      maxWidth: 320,
      alignSelf: 'flex-end',
    },
    modeButton: {
      marginTop: spacing.sm,
      paddingVertical: spacing.xs,
      paddingHorizontal: spacing.sm,
      borderRadius: 999,
      borderWidth: 1,
      borderColor: colors.border,
      alignItems: 'center',
      backgroundColor: colors.surfaceMuted,
    },
    modeButtonText: {
      fontSize: 12,
      color: colors.textPrimary,
    },
    overlay: {
      position: 'absolute',
      top: -8,
      bottom: -8,
      left: -8,
      right: -8,
      backgroundColor: 'rgba(0,0,0,0.08)',
      borderRadius: 20,
      zIndex: -1,
    },
    topButton: {
      position: 'absolute',
      right: 16,
      top: 18,
      width: 36,
      height: 36,
      borderRadius: 18,
      backgroundColor: colors.surface,
      borderWidth: 1,
      borderColor: colors.border,
      alignItems: 'center',
      justifyContent: 'center',
      ...Platform.select({
        web: { boxShadow: '0px 6px 14px rgba(0,0,0,0.16)' } as any,
        default: {
          shadowColor: '#000',
          shadowOpacity: 0.18,
          shadowRadius: 12,
          shadowOffset: { width: 0, height: 6 },
          elevation: 6,
        },
      }),
    },
    fabDot: {
      width: 12,
      height: 12,
      borderRadius: 6,
    },
    closeButton: {
      alignItems: 'flex-end',
      marginBottom: spacing.xs,
    },
    closeIcon: {
      width: 20,
      height: 20,
      borderRadius: 10,
      backgroundColor: colors.surface,
      borderWidth: 1,
      borderColor: colors.border,
      alignItems: 'center',
      justifyContent: 'center',
      ...Platform.select({
        web: { boxShadow: '0px 4px 10px rgba(0,0,0,0.14)' } as any,
        default: {
          shadowColor: '#000',
          shadowOpacity: 0.14,
          shadowRadius: 8,
          shadowOffset: { width: 0, height: 4 },
          elevation: 4,
        },
      }),
    },
    closeBar: {
      position: 'absolute',
      width: 10,
      height: 2,
      borderRadius: 2,
      backgroundColor: colors.textSecondary,
    },
  });
}
