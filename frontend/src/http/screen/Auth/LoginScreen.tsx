import { useEffect, useMemo, useRef } from 'react';
import { Animated, Platform, Pressable, StyleSheet, Text, TextInput } from 'react-native';
import { useRouter } from 'expo-router';
import { PageLayout } from '../../components/PageLayout';
import { Section } from '../../components/Section';
import { ThemeColors, spacing, typography, useTheme } from '../../../support/theme/tokens';
import { useLoginViewModel } from './LoginViewModel';

const useNativeDriver = Platform.OS !== 'web';

export default function LoginScreen() {
  const { theme } = useTheme();
  const styles = useMemo(() => createStyles(theme.colors), [theme.colors]);
  const router = useRouter();
  const { email, password, isSubmitting, error, fieldErrors, setEmail, setPassword, submit } =
    useLoginViewModel();
  const reveals = useRef([new Animated.Value(0), new Animated.Value(0)]).current;

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

  return (
    <PageLayout title="Connexion" subtitle="Accede a ton espace poetique." contentStyle={styles.content}>
      <Animated.View style={[styles.header, revealStyle(reveals[0])]}>
        <Text style={styles.kicker}>Poesi</Text>
      </Animated.View>

      <Animated.View style={revealStyle(reveals[1])}>
        <Section>
          <Text style={styles.label}>Email</Text>
          <TextInput
            style={styles.input}
            value={email}
            onChangeText={setEmail}
            autoCapitalize="none"
            autoCorrect={false}
            keyboardType="email-address"
            placeholder="mon-mail@email.fr"
            placeholderTextColor={theme.colors.textMuted}
          />
          {fieldErrors.email ? <Text style={styles.error}>{fieldErrors.email.join(' ')}</Text> : null}

          <Text style={styles.label}>Mot de passe</Text>
          <TextInput
            style={styles.input}
            value={password}
            onChangeText={setPassword}
            secureTextEntry
            placeholder="********"
            placeholderTextColor={theme.colors.textMuted}
          />
          {fieldErrors.password ? (
            <Text style={styles.error}>{fieldErrors.password.join(' ')}</Text>
          ) : null}
          {error ? <Text style={styles.error}>{error}</Text> : null}

          <Pressable
            style={[styles.button, isSubmitting && styles.buttonDisabled]}
            onPress={submit}
            disabled={isSubmitting}
          >
            <Text style={styles.buttonText}>
              {isSubmitting ? 'Connexion...' : 'Se connecter'}
            </Text>
          </Pressable>

          <Text style={styles.textLine}>
            Pas encore de compte ?{' '}
            <Text style={styles.linkWord} onPress={() => router.push('/(auth)/register')}>
              S'inscrire
            </Text>
          </Text>
          <Text style={styles.textLine}>
            Retour a l'{' '}
            <Text style={styles.linkWord} onPress={() => router.push('/(tabs)/home')}>
              accueil
            </Text>
          </Text>
        </Section>
      </Animated.View>
    </PageLayout>
  );
}

function createStyles(colors: ThemeColors) {
  return StyleSheet.create({
    content: {
      justifyContent: 'center',
    },
    header: {
      marginBottom: spacing.sm,
    },
    kicker: {
      fontSize: typography.caption,
      color: colors.textMuted,
      fontFamily: typography.fontFamily,
      textTransform: 'uppercase',
      letterSpacing: 1,
    },
    label: {
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textMuted,
      marginTop: spacing.sm,
    },
    input: {
      fontSize: typography.body,
      color: colors.textPrimary,
      marginTop: spacing.xs,
      paddingVertical: spacing.sm,
      borderBottomWidth: 1,
      borderBottomColor: colors.border,
      fontFamily: typography.fontFamily,
    },
    error: {
      marginTop: spacing.sm,
      color: colors.danger,
      fontFamily: typography.fontFamily,
    },
    button: {
      marginTop: spacing.md,
      backgroundColor: colors.accent,
      paddingVertical: spacing.sm,
      borderRadius: 999,
      alignItems: 'center',
    },
    buttonDisabled: {
      opacity: 0.6,
    },
    buttonText: {
      color: '#fff',
      fontSize: typography.body,
      fontFamily: typography.headingFont,
      fontWeight: '700',
    },
    textLine: {
      marginTop: spacing.xs,
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
      textAlign: 'center',
    },
    linkWord: {
      fontFamily: typography.headingFont,
      fontWeight: '700',
      color: colors.accentStrong,
    },
  });
}

