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
          default: { marginHorizontal: spacing.lg },
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
  const { toggle, mode } = useTheme();
  const label = mode === 'dark' ? 'Light' : 'Dark';
  return (
    <View
      style={
        Platform.select({
          web: { position: 'fixed', right: 24, top: 18, zIndex: 2000 } as any,
          default: { position: 'absolute', right: 24, top: 18, zIndex: 2000 },
        }) as any
      }
    >
      <Pressable onPress={toggle} style={{ padding: 8, borderRadius: 8, backgroundColor: 'transparent' }}>
        <Text style={{ color: mode === 'dark' ? '#FFF' : '#111' }}>{label}</Text>
      </Pressable>
    </View>
  );
}
