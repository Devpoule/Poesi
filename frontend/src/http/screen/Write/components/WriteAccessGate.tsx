import { Animated, Platform, Pressable, Text, View } from 'react-native';
import { useRouter } from 'expo-router';
import { Screen } from '../../../components/Screen';
import { Button } from '../../../components/Button';
import { useRevealAnimation } from '../hooks/useRevealAnimation';
import { useStyles } from '../styles';

/**
 * Access gate shown to guests before entering the editor.
 */
export function WriteAccessGate() {
  const styles = useStyles();
  const router = useRouter();
  const { reveals, revealStyle } = useRevealAnimation(3);

  return (
    <Screen scroll contentStyle={styles.page}>
      <Animated.View style={revealStyle(reveals[0])}>
        <View style={styles.header}>
          <Text style={styles.title}>Écriture</Text>
          <Text style={styles.subtitle}>Réservée aux auteurs connectés.</Text>
        </View>
      </Animated.View>

      <Animated.View style={[styles.card, revealStyle(reveals[1])]}>
        <Text style={styles.sectionTitle}>Pour écrire</Text>
        <Text style={styles.hint}>
          Connecte-toi ou crée un compte pour ouvrir ton espace d'écriture.
        </Text>
        <View style={styles.gateActions}>
          {Platform.OS === 'web' ? (
            <>
              <Button title="Se connecter" onPress={() => router.push('/(auth)/login')} />
              <Button title="S'inscrire" onPress={() => router.push('/(auth)/register')} variant="secondary" />
            </>
          ) : (
            <>
              <Pressable
                style={styles.gatePrimaryButton}
                onPress={() => router.push('/(auth)/login')}
              >
                <Text style={styles.gatePrimaryText}>Se connecter</Text>
              </Pressable>
              <Pressable
                style={styles.gateSecondaryButton}
                onPress={() => router.push('/(auth)/register')}
              >
                <Text style={styles.gateSecondaryText}>S'inscrire</Text>
              </Pressable>
            </>
          )}
        </View>
      </Animated.View>

      <Animated.View style={[styles.previewCard, revealStyle(reveals[2])]}>
        <Text style={styles.previewTitle}>Aperçu</Text>
        <Text style={styles.previewText}>
          Un éditeur plein écran, silencieux, pour laisser les mots respirer.
        </Text>
      </Animated.View>
    </Screen>
  );
}
