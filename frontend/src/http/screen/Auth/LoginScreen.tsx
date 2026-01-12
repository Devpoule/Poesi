import { useEffect, useRef } from 'react';
import {
  Animated,
  Platform,
  Pressable,
  StyleSheet,
  Text,
  TextInput,
  View,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Screen } from '../../components/Screen';
import { colors, spacing, typography } from '../../../support/theme/tokens';
import { useLoginViewModel } from './LoginViewModel';

const useNativeDriver = Platform.OS !== 'web';

export default function LoginScreen() {
  const router = useRouter();
  const { email, password, isSubmitting, error, setEmail, setPassword, submit } =
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
    <Screen contentStyle={styles.content}>
      <Animated.View style={[styles.header, revealStyle(reveals[0])]}>
        <Text style={styles.kicker}>Poesi</Text>
        <Text style={styles.title}>Connexion</Text>
        <Text style={styles.subtitle}>Accède à ton espace poétique.</Text>
      </Animated.View>

      <Animated.View style={[styles.card, revealStyle(reveals[1])]}>
        <Text style={styles.label}>Email</Text>
        <TextInput
          style={styles.input}
          value={email}
          onChangeText={setEmail}
          autoCapitalize="none"
          autoCorrect={false}
          keyboardType="email-address"
          placeholder="ton@email.fr"
          placeholderTextColor={colors.textMuted}
        />
        <Text style={styles.label}>Mot de passe</Text>
        <TextInput
          style={styles.input}
          value={password}
          onChangeText={setPassword}
          secureTextEntry
          placeholder="********"
          placeholderTextColor={colors.textMuted}
        />
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
        <Text style={styles.helper}>
          Besoin d'un accès ? Contacte le cercle Poesi.
        </Text>
        <Pressable style={styles.link} onPress={() => router.push('/(auth)/register')}>
          <Text style={styles.linkText}>Pas encore de compte ? S'inscrire</Text>
        </Pressable>
      </Animated.View>
    </Screen>
  );
}

const styles = StyleSheet.create({
  content: {
    justifyContent: 'center',
  },
  header: {
    marginBottom: spacing.lg,
  },
  kicker: {
    fontSize: typography.caption,
    color: colors.textMuted,
    fontFamily: typography.fontFamily,
    textTransform: 'uppercase',
    letterSpacing: 1,
  },
  title: {
    fontSize: typography.display,
    fontFamily: typography.fontFamily,
    color: colors.textPrimary,
    marginTop: spacing.xs,
  },
  subtitle: {
    fontSize: typography.body,
    fontFamily: typography.fontFamily,
    color: colors.textSecondary,
    marginTop: spacing.xs,
  },
  card: {
    backgroundColor: colors.surface,
    borderRadius: 20,
    padding: spacing.md,
    borderWidth: 1,
    borderColor: colors.border,
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
    color: colors.textPrimary,
    fontSize: typography.caption,
    fontFamily: typography.fontFamily,
  },
  helper: {
    marginTop: spacing.md,
    fontSize: typography.caption,
    fontFamily: typography.fontFamily,
    color: colors.textMuted,
    textAlign: 'center',
  },
  link: {
    marginTop: spacing.sm,
    alignItems: 'center',
  },
  linkText: {
    fontSize: typography.caption,
    fontFamily: typography.fontFamily,
    color: colors.textSecondary,
  },
});
