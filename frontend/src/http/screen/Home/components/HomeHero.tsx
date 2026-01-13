import { Platform, Pressable, Text, View } from 'react-native';
import { useStyles } from '../styles';
import { Button } from '../../../components/Button';
import { heroBadges } from '../utils/highlights';

type HomeHeroProps = {
  onExplore: () => void;
  onWrite: () => void;
  writeLabel: string;
};

/**
 * Hero card with core brand messaging and entry actions.
 */
export function HomeHero({ onExplore, onWrite, writeLabel }: HomeHeroProps) {
  const styles = useStyles();
  return (
    <View style={styles.heroWrapper}>
      <View style={styles.heroCard}>
        <View style={styles.heroAtmos}>
          <View style={styles.heroGlow} />
          <View style={styles.heroHalo} />
          <View style={styles.heroBeam} />
        </View>
        <Text style={styles.heroKicker}>MAISON POESI</Text>
        <Text style={styles.heroTitle}>La maison des textes sensibles.</Text>
        <Text style={styles.heroSubtitle}>
          Écrire, lire, laisser la résonance faire son oeuvre.
        </Text>
        <View style={styles.heroBadges}>
          {heroBadges.map((badge) => (
            <View key={badge} style={styles.heroBadge}>
              <Text style={styles.heroBadgeText}>{badge}</Text>
            </View>
          ))}
        </View>
        <View style={styles.heroActions}>
          {Platform.OS === 'web' ? (
            <>
              <Button title="Explorer la galerie" onPress={onExplore} variant="primary" />
              <Button title={writeLabel} onPress={onWrite} variant="secondary" />
            </>
          ) : (
            <>
              <Pressable style={styles.primaryButton} onPress={onExplore}>
                <Text style={styles.primaryButtonText}>Explorer la galerie</Text>
              </Pressable>
              <Pressable style={styles.secondaryButton} onPress={onWrite}>
                <Text style={styles.secondaryButtonText}>{writeLabel}</Text>
              </Pressable>
            </>
          )}
        </View>
      </View>
    </View>
  );
}
