import { useEffect, useMemo, useRef } from 'react';
import { Animated, Platform, Pressable, StyleSheet, Text, View, useWindowDimensions } from 'react-native';
import { useRouter } from 'expo-router';
import { useAuth } from '../../../bootstrap/AuthProvider';
import { Screen } from '../../components/Screen';
import { CardPortrait } from '../../components/CardPortrait';
import { PageHeader } from '../../components/PageHeader';
import { Section } from '../../components/Section';
import { SidePanel } from '../../components/SidePanel';
import { ThemeColors, spacing, typography, useTheme } from '../../../support/theme/tokens';

const useNativeDriver = Platform.OS !== 'web';

export default function ProfileScreen() {
  const { theme } = useTheme();
  const styles = useMemo(() => createStyles(theme.colors), [theme.colors]);
  const { logout, tokens } = useAuth();
  const router = useRouter();
  const { width } = useWindowDimensions();
  const isWeb = Platform.OS === 'web';
  const isWide = isWeb && width >= 1100;
  const reveals = useRef([
    new Animated.Value(0),
    new Animated.Value(0),
    new Animated.Value(0),
    new Animated.Value(0),
  ]).current;

  useEffect(() => {
    if (!tokens) {
      router.replace('/(auth)/login');
    }
  }, [tokens, router]);

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

  const sessionLabel = tokens ? 'Connecte' : 'Lecteur';
  const pseudo = tokens ? 'Auteur' : 'Lecteur';
  const totemKey = 'egg';
  const totemName = 'Oeuf';
  const totemBadges: Record<string, any> = {
    egg: require('../../../../assets/totems/badges/totem_badge_egg.png'),
    crow: require('../../../../assets/totems/badges/totem_badge_crow.png'),
    falcon: require('../../../../assets/totems/badges/totem_badge_falcon.png'),
    owl: require('../../../../assets/totems/badges/totem_badge_owl.png'),
    parrot: require('../../../../assets/totems/badges/totem_badge_parrot.png'),
    sparrow: require('../../../../assets/totems/badges/totem_badge_sparrow.png'),
    swan: require('../../../../assets/totems/badges/totem_badge_swan.png'),
  };
  const badgeSource = totemBadges[totemKey] ?? totemBadges.egg;
  const totemCards: Record<string, any> = {
    egg: require('../../../../assets/totems/cards/totem_card_egg.png'),
    crow: require('../../../../assets/totems/cards/totem_card_crow.png'),
    falcon: require('../../../../assets/totems/cards/totem_card_falcon.png'),
    owl: require('../../../../assets/totems/cards/totem_card_owl.png'),
    parrot: require('../../../../assets/totems/cards/totem_card_parrot.png'),
    sparrow: require('../../../../assets/totems/cards/totem_card_sparrow.png'),
    swan: require('../../../../assets/totems/cards/totem_card_swan.png'),
  };
  const cardSource = totemCards[totemKey] ?? totemCards.egg;

  const handleLogout = async () => {
    await logout();
    router.replace('/(auth)/login');
  };

  return (
    <Screen scroll>
      {isWide ? (
        <View style={styles.sidePanel}>
          <SidePanel width={200}>
            <CardPortrait source={cardSource} />
          </SidePanel>
        </View>
      ) : null}

      <PageHeader title="Profil" subtitle="Ton refuge, tes traces, ton rythme." />

      <Animated.View style={revealStyle(reveals[0])}>
        <Section style={styles.identityCard}>
          <View style={styles.identityRow}>
            <CardPortrait source={cardSource} style={styles.avatarCard} />
            <View style={{ flex: 1 }}>
              <Text style={styles.identityLabel}>Pseudo</Text>
              <Text style={styles.identityValue}>{pseudo}</Text>
              <Pressable
                style={styles.totemRow}
                onPress={() =>
                  router.push({ pathname: '/(tabs)/guide/totems', params: { totem: totemKey } })
                }
              >
                <View style={styles.totemBadge}>
                  <Text style={styles.totemBadgeText}>{totemName}</Text>
                </View>
              </Pressable>
            </View>
          </View>
        </Section>
      </Animated.View>

      <Animated.View style={revealStyle(reveals[1])}>
        <Section>
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
        </Section>
      </Animated.View>

      <Animated.View style={revealStyle(reveals[2])}>
        <Section style={styles.sessionCard}>
          <View style={styles.sessionHeader}>
            <Text style={styles.sectionTitle}>Session</Text>
            <View
              style={[
                styles.sessionBadge,
                { backgroundColor: tokens ? theme.colors.accentSoft : theme.colors.surfaceMuted },
              ]}
            >
              <Text
                style={[
                  styles.sessionBadgeText,
                  { color: tokens ? theme.colors.accentStrong : theme.colors.textSecondary },
                ]}
              >
                {sessionLabel}
              </Text>
            </View>
          </View>

          <Text style={styles.sessionCopy}>
            {tokens
              ? 'Tu es connecte. Prends le temps de naviguer en douceur.'
              : 'Connecte-toi pour retrouver tes traces et ton rythme.'}
          </Text>

          {tokens ? (
            <Pressable style={styles.logoutButton} onPress={handleLogout}>
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
        </Section>
      </Animated.View>
    </Screen>
  );
}

function createStyles(colors: ThemeColors) {
  return StyleSheet.create({
    sidePanel: {
      position: 'fixed',
      left: spacing.md,
      top: 180,
      zIndex: 900,
      pointerEvents: 'none',
    },
    identityCard: {
      backgroundColor: colors.surfaceElevated,
      borderColor: 'transparent',
      ...Platform.select({
        web: { boxShadow: '0px 12px 30px rgba(0,0,0,0.12)' } as any,
        default: {
          shadowColor: '#000',
          shadowOpacity: 0.08,
          shadowRadius: 10,
          shadowOffset: { width: 0, height: 6 },
          elevation: 5,
        },
      }),
    },
    identityRow: {
      flexDirection: 'row',
      alignItems: 'center',
      gap: spacing.md,
    },
    avatarCard: {
      width: 110,
      maxWidth: '30%',
      alignSelf: 'flex-start',
      borderRadius: 16,
      ...Platform.select({
        web: {
          width: 96,
          maxWidth: '26%',
        },
        default: {},
      }),
    },
    identityLabel: {
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
    },
    identityValue: {
      fontSize: typography.title,
      fontFamily: typography.headingFont,
      color: colors.textPrimary,
    },
    totemRow: {
      flexDirection: 'row',
      alignItems: 'center',
      gap: spacing.sm,
      marginTop: spacing.xs,
    },
    totemBadge: {
      paddingHorizontal: spacing.sm,
      paddingVertical: 4,
      borderRadius: 999,
      backgroundColor: colors.surfaceMuted,
      borderWidth: 1,
      borderColor: colors.border,
    },
    totemBadgeText: {
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textPrimary,
    },
    sectionTitle: {
      fontSize: typography.body,
      fontFamily: typography.headingFont,
      color: colors.textPrimary,
      marginBottom: spacing.xs,
    },
    statsRow: {
      flexDirection: 'row',
      alignItems: 'center',
      justifyContent: 'space-between',
      marginTop: spacing.sm,
      gap: spacing.sm,
    },
    stat: {
      alignItems: 'center',
      flex: 1,
      paddingVertical: spacing.xs,
      borderRadius: 12,
      backgroundColor: colors.surfaceMuted,
    },
    statValue: {
      fontSize: typography.body,
      fontFamily: typography.headingFont,
      color: colors.textPrimary,
    },
    statLabel: {
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
    },
    sessionCard: {
      backgroundColor: colors.surfaceElevated,
      borderColor: 'transparent',
      ...Platform.select({
        web: { boxShadow: '0px 14px 38px rgba(0,0,0,0.18)' } as any,
        default: {
          shadowColor: '#000',
          shadowOpacity: 0.12,
          shadowRadius: 12,
          shadowOffset: { width: 0, height: 6 },
          elevation: 6,
        },
      }),
    },
    sessionHeader: {
      flexDirection: 'row',
      alignItems: 'center',
      justifyContent: 'space-between',
    },
    sessionBadge: {
      paddingHorizontal: spacing.sm,
      paddingVertical: 4,
      borderRadius: 999,
      borderWidth: 1,
      borderColor: colors.border,
    },
    sessionBadgeText: {
      fontSize: typography.caption,
      fontFamily: typography.headingFont,
    },
    sessionCopy: {
      marginTop: spacing.xs,
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
      lineHeight: 18,
    },
    sessionActions: {
      flexDirection: 'row',
      justifyContent: 'space-between',
      marginTop: spacing.sm,
      gap: spacing.sm,
    },
    primaryButton: {
      flex: 1,
      backgroundColor: colors.accent,
      paddingVertical: spacing.sm,
      borderRadius: 999,
      alignItems: 'center',
    },
    primaryButtonText: {
      color: '#fff',
      fontSize: typography.body,
      fontFamily: typography.headingFont,
      fontWeight: '700',
    },
    secondaryButton: {
      flex: 1,
      backgroundColor: colors.surfaceMuted,
      paddingVertical: spacing.sm,
      borderRadius: 999,
      alignItems: 'center',
      borderWidth: 1,
      borderColor: colors.border,
    },
    secondaryButtonText: {
      color: colors.textPrimary,
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
    },
    logoutButton: {
      marginTop: spacing.sm,
      paddingVertical: spacing.sm,
      borderRadius: 999,
      backgroundColor: colors.surface,
      alignItems: 'center',
      borderWidth: 1,
      borderColor: colors.border,
    },
    logoutText: {
      fontSize: typography.caption,
      fontFamily: typography.headingFont,
      color: colors.textPrimary,
    },
  });
}
