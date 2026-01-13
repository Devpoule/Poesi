import { Platform, Pressable, Text, View } from 'react-native';
import { useStyles } from '../styles';
import { Button } from '../../../components/Button';
import { heroBadges } from '../utils/highlights';

type HomeHeroProps = {
  onExplore: () => void;
  onWrite: () => void;
  onGuide: () => void;
  writeLabel: string;
};

/**
 * Hero card with core brand messaging and entry actions.
 */
export function HomeHero({ onExplore, onWrite, onGuide, writeLabel }: HomeHeroProps) {
  const styles = useStyles();
  return (
    <View style={styles.heroWrapper}>
      <View style={styles.heroCard}>
        <View style={styles.heroAtmos}>
          <View style={styles.heroGlow} />
          <View style={styles.heroHalo} />
          <View style={styles.heroBeam} />
        </View>
        <Text style={styles.heroKicker}>POESI</Text>
        <Text style={styles.heroTitle}>L'endroit où écrire</Text>
        <Text style={styles.heroSubtitle}>
          Ecrire, lire, laisser la resonance faire son oeuvre.
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
              <Button
                title={writeLabel}
                onPress={onWrite}
                variant="secondary"
                style={styles.heroButton}
              />
              <Button
                title="Decouvrir le guide"
                onPress={onGuide}
                variant="secondary"
                style={styles.heroButton}
              />
            </>
          ) : (
            <>
              <Pressable style={styles.primaryButton} onPress={onExplore}>
                <Text style={styles.primaryButtonText}>Explorer la galerie</Text>
              </Pressable>
              <Pressable style={styles.secondaryButton} onPress={onWrite}>
                <Text style={styles.secondaryButtonText}>{writeLabel}</Text>
              </Pressable>
              <Pressable style={styles.secondaryButton} onPress={onGuide}>
                <Text style={styles.secondaryButtonText}>Decouvrir le guide</Text>
              </Pressable>
            </>
          )}
        </View>
      </View>
    </View>
  );
}
