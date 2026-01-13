import { Text, View } from 'react-native';
import { useStyles } from '../styles';
import { SaveIndicator } from './SaveIndicator';

type WriteHeaderProps = {
  moodAccent: string;
  draftTooltip: string;
  isDraftActive: boolean;
  onDraftToggle: () => void;
};

/**
 * Header row with title, subtitle, and draft toggle.
 */
export function WriteHeader({
  moodAccent,
  draftTooltip,
  isDraftActive,
  onDraftToggle,
}: WriteHeaderProps) {
  const styles = useStyles();
  return (
    <View style={styles.header}>
      <View style={styles.headerRow}>
        <Text style={styles.title}>Atelier d'écriture &#x270E;</Text>
        <SaveIndicator
          label={draftTooltip}
          color={moodAccent}
          active={isDraftActive}
          onToggle={onDraftToggle}
        />
      </View>
      <Text style={styles.subtitle}>Un espace silencieux, sans mesure ni compteur.</Text>
    </View>
  );
}
