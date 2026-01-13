import { SafeAreaView, StatusBar, StyleSheet, Text, View } from 'react-native';
import { useMemo } from 'react';
import { ThemeColors, spacing, typography, useTheme } from './src/support/theme/tokens';

export default function App() {
  const { theme } = useTheme();
  const styles = useMemo(() => createStyles(theme.colors), [theme.colors]);
  return (
    <SafeAreaView style={styles.safeArea}>
      <StatusBar barStyle="dark-content" />
      <View style={styles.container}>
        <Text style={styles.title}>POESI</Text>
        <Text style={styles.subtitle}>Écriture, lecture, résonance.</Text>
        <Text style={styles.caption}>Frontend Expo + RN Web + Mercure</Text>
      </View>
    </SafeAreaView>
  );
}

function createStyles(colors: ThemeColors) {
  return StyleSheet.create({
  safeArea: {
    flex: 1,
    backgroundColor: colors.background,
  },
  container: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    padding: spacing.lg,
  },
  title: {
    fontSize: typography.title,
    fontFamily: typography.fontFamily,
    color: colors.textPrimary,
    marginBottom: spacing.sm,
  },
  subtitle: {
    fontSize: typography.body,
    fontFamily: typography.fontFamily,
    color: colors.textSecondary,
    marginBottom: spacing.xs,
  },
  caption: {
    fontSize: typography.caption,
    fontFamily: typography.fontFamily,
    color: colors.textMuted,
  },
  });
}
}
