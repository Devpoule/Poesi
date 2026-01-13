import { Tabs } from 'expo-router';
import { Platform, Pressable, Text, View } from 'react-native';
import { TabItem } from '../../src/http/components/TabItem';
import { spacing, layout, useTheme } from '../../src/support/theme/tokens';

export default function TabsLayout() {
  return (
    <>
      <InnerTabs />
      <ThemeToggle />
    </>
  );
}
function InnerTabs() {
  const { theme } = useTheme();
  const colors = theme.colors;
  return (
    <Tabs
      screenOptions={{
        headerShown: false,
        tabBarShowLabel: false,
        tabBarStyle: {
          backgroundColor: colors.surfaceElevated,
          borderTopColor: 'transparent',
          height: Platform.select({ web: 64, default: 72 }) as number,
          paddingHorizontal: spacing.lg,
          paddingTop: spacing.xs,
          paddingBottom: spacing.xs,
          overflow: 'visible',
          position: Platform.select({ web: 'fixed', default: 'absolute' }) as any,
          left: layout.sidePercent,
          right: layout.sidePercent,
          top: Platform.OS === 'web' ? 14 : undefined,
          bottom: Platform.OS === 'web' ? undefined : 14,
          borderRadius: 24,
          zIndex: Platform.select({ web: 1000, default: undefined }) as any,
          ...Platform.select({
            web: { boxShadow: '0px 12px 30px rgba(0,0,0,0.25)' } as any,
            default: {
              shadowColor: '#000',
              shadowOpacity: 0.18,
              shadowRadius: 18,
              shadowOffset: { width: 0, height: 10 },
              elevation: 8,
            },
          }),
        },
        tabBarItemStyle: Platform.select({
          web: { marginHorizontal: spacing.xs / 2 } as any,
          default: { marginHorizontal: spacing.sm },
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

// floating toggle placed above tabs on web
function ThemeToggle() {
  const { toggle, mode, theme } = useTheme();
  const label = mode === 'dark' ? 'Clair' : 'Sombre';
  const isDark = mode === 'dark';
  return (
    <View
      style={
        Platform.select({
          web: { position: 'fixed', right: 24, top: 18, zIndex: 2000 } as any,
          default: { position: 'absolute', right: 24, top: 18, zIndex: 2000 },
        }) as any
      }
    >
      <Pressable
        onPress={toggle}
        style={{
          paddingVertical: 6,
          paddingHorizontal: 10,
          borderRadius: 999,
          backgroundColor: isDark ? theme.colors.surfaceElevated : theme.colors.surface,
          borderWidth: 1,
          borderColor: theme.colors.border,
          flexDirection: 'row',
          alignItems: 'center',
        }}
      >
        <View
          style={{
            width: 16,
            height: 16,
            borderRadius: 8,
            backgroundColor: isDark ? theme.colors.textPrimary : theme.colors.textPrimary,
            marginRight: 6,
          }}
        >
          {isDark ? (
            <View
              style={{
                position: 'absolute',
                width: 10,
                height: 10,
                borderRadius: 5,
                backgroundColor: theme.colors.background,
                right: -1,
                top: 3,
              }}
            />
          ) : (
            <>
              <View
                style={{
                  position: 'absolute',
                  width: 2,
                  height: 6,
                  backgroundColor: theme.colors.textPrimary,
                  left: 7,
                  top: -4,
                  borderRadius: 2,
                }}
              />
              <View
                style={{
                  position: 'absolute',
                  width: 2,
                  height: 6,
                  backgroundColor: theme.colors.textPrimary,
                  left: 7,
                  bottom: -4,
                  borderRadius: 2,
                }}
              />
              <View
                style={{
                  position: 'absolute',
                  width: 6,
                  height: 2,
                  backgroundColor: theme.colors.textPrimary,
                  right: -4,
                  top: 7,
                  borderRadius: 2,
                }}
              />
              <View
                style={{
                  position: 'absolute',
                  width: 6,
                  height: 2,
                  backgroundColor: theme.colors.textPrimary,
                  left: -4,
                  top: 7,
                  borderRadius: 2,
                }}
              />
            </>
          )}
        </View>
        <Text style={{ color: theme.colors.textPrimary }}>{label}</Text>
      </Pressable>
    </View>
  );
}
