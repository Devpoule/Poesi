import { useEffect, useMemo, useRef } from 'react';
import { Animated, Platform, Pressable, StyleSheet, Text, View } from 'react-native';
import { useAuth } from '../../../bootstrap/AuthProvider';
import { Screen } from '../../components/Screen';
import { ThemeColors, spacing, typography, useTheme } from '../../../support/theme/tokens';

const useNativeDriver = Platform.OS !== 'web';

export default function ProfileScreen() {
  const { theme } = useTheme();
  const styles = useMemo(() => createStyles(theme.colors), [theme.colors]);
  const { logout, tokens } = useAuth();
  const reveals = useRef([
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

  const sessionLabel = tokens ? 'Connecte' : 'Deconnecte';

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
        <Text style={styles.hint}>
          Le totem incarne la posture d'ecriture du moment.
        </Text>
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
          <Text style={styles.hint}>
            Expiration refresh: {tokens.refreshTokenExpiresAt}
          </Text>
        ) : null}
        <Pressable style={styles.logoutButton} onPress={logout}>
          <Text style={styles.logoutText}>Se deconnecter</Text>
        </Pressable>
      </Animated.View>
    </Screen>
  );
}

function createStyles(colors: ThemeColors) {
  return StyleSheet.create({
    header: {
      marginBottom: spacing.md,
    },
    title: {
      fontSize: typography.display,
      fontFamily: typography.fontFamily,
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
      fontFamily: typography.fontFamily,
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
  });
}
