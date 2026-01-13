import { useMemo } from 'react';
import { Redirect, Stack, useSegments } from 'expo-router';
import { LogBox, StatusBar, StyleSheet, Text, View } from 'react-native';
import { AuthProvider, useAuth } from '../src/bootstrap/AuthProvider';
import { ThemeColors, ThemeProvider, spacing, typography, useTheme } from '../src/support/theme/tokens';

LogBox.ignoreLogs(['props.pointerEvents is deprecated. Use style.pointerEvents']);
const warnFilterKey = '__poesiWarnFilter';
if (!(globalThis as any)[warnFilterKey]) {
  (globalThis as any)[warnFilterKey] = true;
  const originalWarn = console.warn;
  console.warn = (...args: unknown[]) => {
    if (typeof args[0] === 'string' && args[0].includes('props.pointerEvents is deprecated')) {
      return;
    }
    originalWarn(...args);
  };
}

function AuthGate({ children }: { children: React.ReactNode }) {
  const { tokens, isLoading } = useAuth();
  const { theme } = useTheme();
  const styles = useMemo(() => createStyles(theme.colors), [theme.colors]);
  const segments = useSegments() as string[];
  const inAuthGroup = segments[0] === '(auth)';
  const publicTabs = new Set(['home', 'poems', 'feed', 'write', 'guide']);
  const isPublicTab =
    segments[0] === '(tabs)' && publicTabs.has(segments[1] ?? '');

  if (isLoading) {
    return (
      <View style={styles.loading}>
        <Text style={styles.loadingText}>Chargement...</Text>
      </View>
    );
  }

  if (!tokens && !inAuthGroup && !isPublicTab) {
    return <Redirect href="/(auth)/login" />;
  }

  if (tokens && inAuthGroup) {
    return <Redirect href="/(tabs)/home" />;
  }

  return <>{children}</>;
}

export default function RootLayout() {
  return (
    <ThemeProvider>
      <AuthProvider>
        <AuthGate>
          <ThemedStatusBar />
          <Stack screenOptions={{ headerShown: false }}>
            <Stack.Screen name="(auth)" />
            <Stack.Screen name="(tabs)" />
          </Stack>
        </AuthGate>
      </AuthProvider>
    </ThemeProvider>
  );
}

function ThemedStatusBar() {
  const { mode } = useTheme();
  return <StatusBar barStyle={mode === 'dark' ? 'light-content' : 'dark-content'} />;
}

function createStyles(colors: ThemeColors) {
  return StyleSheet.create({
    loading: {
      flex: 1,
      alignItems: 'center',
      justifyContent: 'center',
      backgroundColor: colors.background,
      padding: spacing.lg,
    },
    loadingText: {
      fontSize: typography.body,
      color: colors.textSecondary,
    },
  });
}
