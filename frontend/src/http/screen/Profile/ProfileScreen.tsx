import { useEffect, useMemo, useRef } from 'react';
import { Animated, Platform, Pressable, StyleSheet, Text, View } from 'react-native';
import { useRouter } from 'expo-router';
import { useAuth } from '../../../bootstrap/AuthProvider';
import { Screen } from '../../components/Screen';
import { ThemeColors, spacing, typography, useTheme } from '../../../support/theme/tokens';

const useNativeDriver = Platform.OS !== 'web';

export default function ProfileScreen() {
  const { theme, mode, toggleMode } = useTheme();
  const styles = useMemo(() => createStyles(theme.colors), [theme.colors]);
  const { logout, tokens } = useAuth();
  const router = useRouter();
  const reveals = useRef([
    new Animated.Value(0),
    new Animated.Value(0),
    new Animated.Value(0),
    new Animated.Value(0),
    new Animated.Value(0),
  ]).current;

  useEffect(() => {
    Animated.stagger(
      120,
      reveals.map((value) =>
        Animated.timing(value, {
          toValue: 1,
          duration: 320,
          useNativeDriver,
        })
      )
    ).start();
  }, [reveals]);

  const revealStyle = (value: Animated.Value) => ({
    opacity: value,
    transform: [
      {
        translateY: value.interpolate({
          inputRange: [0, 1],
          outputRange: [10, 0],
        }),
      },
    ],
  });

  const sessionLabel = tokens ? 'Connecte' : 'Invite';

  return (
    <Screen scroll>
      <Animated.View style={[styles.header, revealStyle(reveals[0])]}>
        <Text style={styles.title}>Profil</Text>
        <Text style={styles.subtitle}>Ton refuge, tes traces, ton rythme.</Text>
      </Animated.View>

      <Animated.View style={[styles.card, revealStyle(reveals[1])]}>
        <Text style={styles.sectionTitle}>Totem</Text>
        <View style={styles.rowBetween}>
          <Text style={styles.value}>Oeuf</Text>
          <View style={styles.badge}>
            <Text style={styles.badgeText}>Par defaut</Text>
          </View>
        </View>
        <Text style={styles.hint}>Le totem incarne la posture d'ecriture du moment.</Text>
      </Animated.View>

      <Animated.View style={[styles.card, revealStyle(reveals[2])]}>
        <Text style={styles.sectionTitle}>Bibliotheque</Text>
        <View style={styles.statsRow}>
          <View style={styles.stat}>
            <Text style={styles.statValue}>0</Text>
            <Text style={styles.statLabel}>Textes</Text>
          </View>
          <View style={styles.stat}>
            <Text style={styles.statValue}>0</Text>
            <Text style={styles.statLabel}>Brouillons</Text>
          </View>
          <View style={styles.stat}>
            <Text style={styles.statValue}>0</Text>
            <Text style={styles.statLabel}>Resonances</Text>
          </View>
        </View>
      </Animated.View>

      <Animated.View style={[styles.card, revealStyle(reveals[3])]}>
        <Text style={styles.sectionTitle}>Session</Text>
        <Text style={styles.value}>{sessionLabel}</Text>
        {tokens?.refreshTokenExpiresAt ? (
          <Text style={styles.hint}>Expiration refresh: {tokens.refreshTokenExpiresAt}</Text>
        ) : null}
        {tokens ? (
          <Pressable style={styles.logoutButton} onPress={logout}>
            <Text style={styles.logoutText}>Se deconnecter</Text>
          </Pressable>
        ) : (
          <View style={styles.sessionActions}>
            <Pressable style={styles.primaryButton} onPress={() => router.push('/(auth)/login')}>
              <Text style={styles.primaryButtonText}>Se connecter</Text>
            </Pressable>
            <Pressable
              style={styles.secondaryButton}
              onPress={() => router.push('/(auth)/register')}
            >
              <Text style={styles.secondaryButtonText}>S'inscrire</Text>
            </Pressable>
          </View>
        )}
      </Animated.View>
    </Screen>
  );
}

function ThemeIcon({ mode, colors }: { mode: 'light' | 'dark'; colors: ThemeColors }) {
  const iconColor = colors.textPrimary;
  if (mode === 'dark') {
    return (
      <View style={stylesIcon.moon}>
        <View style={[stylesIcon.moonFill, { backgroundColor: iconColor }]} />
        <View style={[stylesIcon.moonCutout, { backgroundColor: colors.surface }]} />
      </View>
    );
  }
  return (
    <View style={stylesIcon.sun}>
      <View style={[stylesIcon.sunCore, { backgroundColor: iconColor }]} />
      <View style={[stylesIcon.sunRayTop, { backgroundColor: iconColor }]} />
      <View style={[stylesIcon.sunRayBottom, { backgroundColor: iconColor }]} />
      <View style={[stylesIcon.sunRayLeft, { backgroundColor: iconColor }]} />
      <View style={[stylesIcon.sunRayRight, { backgroundColor: iconColor }]} />
    </View>
  );
}

const stylesIcon = StyleSheet.create({
  sun: {
    width: 18,
    height: 18,
    marginRight: spacing.sm,
    alignItems: 'center',
    justifyContent: 'center',
  },
  sunCore: {
    width: 8,
    height: 8,
    borderRadius: 4,
  },
  sunRayTop: {
    position: 'absolute',
    width: 2,
    height: 4,
    top: 0,
    borderRadius: 2,
  },
  sunRayBottom: {
    position: 'absolute',
    width: 2,
    height: 4,
    bottom: 0,
    borderRadius: 2,
  },
  sunRayLeft: {
    position: 'absolute',
    width: 4,
    height: 2,
    left: 0,
    borderRadius: 2,
  },
  sunRayRight: {
    position: 'absolute',
    width: 4,
    height: 2,
    right: 0,
    borderRadius: 2,
  },
  moon: {
    width: 14,
    height: 14,
    borderRadius: 7,
    marginRight: spacing.sm,
    overflow: 'hidden',
  },
  moonFill: {
    width: 14,
    height: 14,
    borderRadius: 7,
  },
  moonCutout: {
    position: 'absolute',
    width: 8,
    height: 8,
    borderRadius: 4,
    right: -1,
    top: 1,
  },
});

function createStyles(colors: ThemeColors) {
  return StyleSheet.create({
    header: {
      marginBottom: spacing.md,
    },
    title: {
      fontSize: typography.display,
      fontFamily: typography.headingFont,
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
    sectionTitle: {
      fontSize: typography.body,
      fontFamily: typography.headingFont,
      color: colors.textPrimary,
      marginBottom: spacing.sm,
    },
    rowBetween: {
      flexDirection: 'row',
      alignItems: 'center',
      justifyContent: 'space-between',
    },
    value: {
      fontSize: typography.body,
      fontFamily: typography.fontFamily,
      color: colors.textPrimary,
    },
    badge: {
      backgroundColor: colors.surfaceMuted,
      paddingVertical: 4,
      paddingHorizontal: spacing.sm,
      borderRadius: 999,
    },
    badgeText: {
      fontSize: typography.small,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
    },
    hint: {
      marginTop: spacing.sm,
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textMuted,
    },
    statsRow: {
      flexDirection: 'row',
      justifyContent: 'space-between',
    },
    stat: {
      alignItems: 'center',
      flex: 1,
    },
    statValue: {
      fontSize: typography.title,
      fontFamily: typography.fontFamily,
      color: colors.textPrimary,
    },
    statLabel: {
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
      marginTop: spacing.xs,
    },
    logoutButton: {
      marginTop: spacing.md,
      borderRadius: 999,
      paddingVertical: spacing.sm,
      alignItems: 'center',
      borderWidth: 1,
      borderColor: colors.border,
    },
    logoutText: {
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textPrimary,
    },
    sessionActions: {
      marginTop: spacing.md,
    },
    primaryButton: {
      backgroundColor: colors.accent,
      paddingVertical: spacing.sm,
      borderRadius: 999,
      alignItems: 'center',
    },
    primaryButtonText: {
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textPrimary,
    },
    secondaryButton: {
      marginTop: spacing.sm,
      borderWidth: 1,
      borderColor: colors.border,
      paddingVertical: spacing.sm,
      borderRadius: 999,
      alignItems: 'center',
      backgroundColor: colors.surface,
    },
    secondaryButtonText: {
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
    },
    lightButton: {
      marginTop: spacing.sm,
      borderWidth: 1,
      borderColor: colors.border,
      paddingVertical: spacing.sm,
      paddingHorizontal: spacing.md,
      borderRadius: 999,
      alignItems: 'center',
      flexDirection: 'row',
      justifyContent: 'center',
      backgroundColor: colors.surface,
    },
    lightButtonText: {
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textPrimary,
    },
  });
}
