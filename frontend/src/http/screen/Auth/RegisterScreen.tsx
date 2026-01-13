import { useEffect, useMemo, useRef, useState } from 'react';
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
import { ThemeColors, spacing, typography, useTheme } from '../../../support/theme/tokens';

const useNativeDriver = Platform.OS !== 'web';

export default function RegisterScreen() {
  const { theme } = useTheme();
  const styles = useMemo(() => createStyles(theme.colors), [theme.colors]);
  const router = useRouter();
  const [email, setEmail] = useState('');
  const [pseudo, setPseudo] = useState('');
  const [password, setPassword] = useState('');
  const [notice, setNotice] = useState<string | null>(null);
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

  const submit = () => {
    setNotice("L'inscription est ouverte sur invitation.");
  };

  return (
    <Screen contentStyle={styles.content}>
      <Animated.View style={[styles.header, revealStyle(reveals[0])]}>
        <Text style={styles.kicker}>Poesi</Text>
        <Text style={styles.title}>Inscription</Text>
        <Text style={styles.subtitle}>Demande un acces pour ecrire.</Text>
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
          placeholderTextColor={theme.colors.textMuted}
        />
        <Text style={styles.label}>Pseudo</Text>
        <TextInput
          style={styles.input}
          value={pseudo}
          onChangeText={setPseudo}
          autoCapitalize="none"
          autoCorrect={false}
          placeholder="ton pseudo"
          placeholderTextColor={theme.colors.textMuted}
        />
        <Text style={styles.label}>Mot de passe</Text>
        <TextInput
          style={styles.input}
          value={password}
          onChangeText={setPassword}
          secureTextEntry
          placeholder="********"
          placeholderTextColor={theme.colors.textMuted}
        />
        {notice ? <Text style={styles.notice}>{notice}</Text> : null}
        <Pressable style={styles.button} onPress={submit}>
          <Text style={styles.buttonText}>Demander un acces</Text>
        </Pressable>
        <Pressable style={styles.link} onPress={() => router.push('/(auth)/login')}>
          <Text style={styles.linkText}>Deja un compte ? Se connecter</Text>
        </Pressable>
      </Animated.View>
    </Screen>
  );
}

function createStyles(colors: ThemeColors) {
  return StyleSheet.create({
  content: {
    justifyContent: 'center',
  },
  header: {
    marginBottom: spacing.lg,
  },
  kicker: {
    fontSize: typography.caption,
    fontFamily: typography.fontFamily,
    color: colors.textMuted,
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
  button: {
    marginTop: spacing.md,
    backgroundColor: colors.accent,
    paddingVertical: spacing.sm,
    borderRadius: 999,
    alignItems: 'center',
  },
  buttonText: {
    color: colors.textPrimary,
    fontSize: typography.caption,
    fontFamily: typography.fontFamily,
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
}
