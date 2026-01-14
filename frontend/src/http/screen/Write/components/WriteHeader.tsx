import { Text, View } from 'react-native';
import { useStyles } from '../styles';
import { SaveIndicator } from './SaveIndicator';

type WriteHeaderProps = {
  moodAccent: string;
  subtitle?: string;
  draftTooltip: string;
  isDraftActive: boolean;
  onDraftToggle: () => void;
};

/**
 * Header row with title, subtitle, and draft toggle.
 */
export function WriteHeader({
  moodAccent,
  subtitle,
  draftTooltip,
  isDraftActive,
  onDraftToggle,
}: WriteHeaderProps) {
  const styles = useStyles();
  return (
    <View style={styles.header}>
      <View style={styles.headerRow}>
        <View style={styles.titleRow}>
          <Text style={styles.title}>Atelier d'ecriture &#x270E;</Text>
        </View>
        <SaveIndicator
          label={draftTooltip}
          color={moodAccent}
          active={isDraftActive}
          onToggle={onDraftToggle}
        />
      </View>
      <Text style={styles.subtitle}>
        {subtitle ?? 'Un espace silencieux, sans mesure ni compteur.'}
      </Text>
    </View>
  );
}
