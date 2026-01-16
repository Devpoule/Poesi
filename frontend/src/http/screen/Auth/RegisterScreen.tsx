import { useEffect, useMemo, useRef, useState } from 'react';
import { Animated, Platform, Pressable, StyleSheet, Text, TextInput } from 'react-native';
import { useRouter } from 'expo-router';
import { PageLayout } from '../../components/PageLayout';
import { Section } from '../../components/Section';
import { useAuth } from '../../../bootstrap/AuthProvider';
import { register } from '../../../infrastructure/api/auth';
import { ApiError } from '../../../infrastructure/api/client';
import { ThemeColors, spacing, typography, useTheme } from '../../../support/theme/tokens';

const useNativeDriver = Platform.OS !== 'web';

export default function RegisterScreen() {
  const { theme } = useTheme();
  const styles = useMemo(() => createStyles(theme.colors), [theme.colors]);
  const router = useRouter();
  const { login } = useAuth();
  const [email, setEmail] = useState('');
  const [pseudo, setPseudo] = useState('');
  const [password, setPassword] = useState('');
  const [notice, setNotice] = useState<string | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [fieldErrors, setFieldErrors] = useState<Record<string, string[]>>({});
  const [isSubmitting, setIsSubmitting] = useState(false);
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

  const submit = async () => {
    setError(null);
    setNotice(null);
    setFieldErrors({});
    setIsSubmitting(true);
    try {
      await register({ email, password, pseudo });
      await login(email, password);
      setNotice('Inscription reussie. Bienvenue !');
      router.replace('/(tabs)/home');
    } catch (err) {
      if (err instanceof ApiError) {
        setError(err.message);
        setFieldErrors(err.errors ?? {});
      } else {
        setError((err as Error).message || 'Inscription impossible.');
      }
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <PageLayout
      title="Inscription"
      subtitle="Inscrire ses paroles, simplement."
      contentStyle={styles.content}
    >
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

          <Text style={styles.label}>Pseudo</Text>
          <TextInput
            style={styles.input}
            value={pseudo}
            onChangeText={setPseudo}
            autoCapitalize="none"
            autoCorrect={false}
            placeholder="mon pseudo"
            placeholderTextColor={theme.colors.textMuted}
          />
          {fieldErrors.pseudo ? <Text style={styles.error}>{fieldErrors.pseudo.join(' ')}</Text> : null}

          <Text style={styles.label}>Mot de passe</Text>
          <TextInput
            style={styles.input}
            value={password}
            onChangeText={setPassword}
            secureTextEntry
            placeholder="********"
            placeholderTextColor={theme.colors.textMuted}
          />
          {fieldErrors.password ? <Text style={styles.error}>{fieldErrors.password.join(' ')}</Text> : null}
          {notice ? <Text style={styles.notice}>{notice}</Text> : null}
          {error ? <Text style={styles.error}>{error}</Text> : null}

          <Pressable
            style={[styles.button, isSubmitting && styles.buttonDisabled]}
            onPress={submit}
            disabled={isSubmitting}
          >
            <Text style={styles.buttonText}>
              {isSubmitting ? 'Inscription...' : "S'inscrire"}
            </Text>
          </Pressable>

          <Text style={styles.textLine}>
            Deja un compte ?{' '}
            <Text style={styles.linkWord} onPress={() => router.push('/(auth)/login')}>
              Se connecter
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
      fontFamily: typography.fontFamily,
      color: colors.textMuted,
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
      fontFamily: typography.fontFamily,
      color: colors.textPrimary,
      marginTop: spacing.xs,
      paddingVertical: spacing.sm,
      borderBottomWidth: 1,
      borderBottomColor: colors.border,
    },
    notice: {
      marginTop: spacing.sm,
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
    },
    error: {
      marginTop: spacing.sm,
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.danger,
    },
    button: {
      marginTop: spacing.md,
      backgroundColor: colors.accent,
      paddingVertical: spacing.sm,
      borderRadius: 999,
      alignItems: 'center',
    },
    buttonDisabled: {
      opacity: 0.65,
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

