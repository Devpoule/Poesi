import { Tabs } from 'expo-router';
import { Platform } from 'react-native';
import { TabItem } from '../../src/http/components/TabItem';
import { colors, spacing, layout } from '../../src/support/theme/tokens';

export default function TabsLayout() {
  return (
    <Tabs
      screenOptions={{
        headerShown: false,
        tabBarShowLabel: false,
        tabBarStyle: {
          backgroundColor: colors.surface,
          borderTopColor: colors.border,
          height: Platform.select({ web: 64, default: 72 }) as number,
          paddingHorizontal: spacing.lg,
          paddingTop: spacing.xs,
          paddingBottom: spacing.xs,
          overflow: 'visible',
          position: Platform.select({ web: 'fixed', default: 'absolute' }) as any,
          left: Platform.OS === 'web' ? layout.sidePercent : spacing.md,
          right: Platform.OS === 'web' ? layout.sidePercent : spacing.md,
          top: Platform.OS === 'web' ? 14 : undefined,
          bottom: Platform.OS === 'web' ? undefined : 14,
          borderRadius: 24,
          zIndex: Platform.select({ web: 1000, default: undefined }) as any,
          ...Platform.select({
            web: { boxShadow: '0px 8px 20px rgba(0,0,0,0.06)' } as any,
            default: {
              shadowColor: '#000',
              shadowOpacity: 0.08,
              shadowRadius: 18,
              shadowOffset: { width: 0, height: 10 },
              elevation: 6,
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
          title: 'Poèmes',
          tabBarIcon: ({ focused }) => <TabItem variant="poems" focused={focused} />,
        }}
      />
      <Tabs.Screen
        name="write"
        options={{
          title: 'Écrire',
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
