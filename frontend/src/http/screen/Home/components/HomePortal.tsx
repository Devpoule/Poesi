import { Platform, Pressable, Text, View } from 'react-native';
import { styles } from '../styles';
import { Button } from '../../../components/Button';

type HomePortalProps = {
  writeLabel: string;
  onWrite: () => void;
};

/**
 * Closing call-to-action panel for the home page.
 */
export function HomePortal({ writeLabel, onWrite }: HomePortalProps) {
  return (
    <View style={styles.portalCard}>
      <Text style={styles.portalTitle}>Entrer dans le refuge</Text>
      <Text style={styles.portalText}>
        Accède à ton espace d'écriture, puis publie en silence.
      </Text>
      {Platform.OS === 'web' ? (
        <Button title={writeLabel} onPress={onWrite} variant="primary" />
      ) : (
        <Pressable style={styles.primaryButton} onPress={onWrite}>
          <Text style={styles.primaryButtonText}>{writeLabel}</Text>
        </Pressable>
      )}
    </View>
  );
}
