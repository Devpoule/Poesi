import { Redirect, Stack, useSegments } from 'expo-router';
import { StyleSheet, Text, View } from 'react-native';
import { AuthProvider, useAuth } from '../src/bootstrap/AuthProvider';
import { colors, spacing, typography } from '../src/support/theme/tokens';

function AuthGate({ children }: { children: React.ReactNode }) {
  const { tokens, isLoading } = useAuth();
  const segments = useSegments();
  const inAuthGroup = segments[0] === '(auth)';
  const publicTabs = new Set(['home', 'poems', 'feed', 'write']);
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
    <AuthProvider>
      <AuthGate>
        <Stack screenOptions={{ headerShown: false }}>
          <Stack.Screen name="(auth)" />
          <Stack.Screen name="(tabs)" />
        </Stack>
      </AuthGate>
    </AuthProvider>
  );
}

const styles = StyleSheet.create({
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
