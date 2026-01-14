import { Text, View } from 'react-native';
import { moodOptions } from '../../../../support/theme/moods';
import { useTheme } from '../../../../support/theme/tokens';
import { getMoodLore } from '../utils/moodLore';
import { useStyles } from '../styles';
import { Button } from '../../../components/Button';
import { MoodBadge } from './MoodBadge';

type WriteMoodPanelProps = {
  selectedMood: string;
  description: string;
  onSelectMood: (key: string) => void;
};

/**
 * Renders the mood selection grid and description.
 */
export function WriteMoodPanel({
  selectedMood,
  description,
  onSelectMood,
}: WriteMoodPanelProps) {
  const styles = useStyles();
  const { accentKey, setAccentKey } = useTheme();
  const canApply = selectedMood !== 'neutre' && accentKey !== selectedMood;
  return (
    <>
      <View style={styles.moodGrid}>
        {moodOptions.map((option) => {
          const lore = getMoodLore(option.key);
          const label = lore?.label ?? option.label;
          return (
            <MoodBadge
              key={option.key}
              label={label}
              color={option.color}
              active={selectedMood === option.key}
              onPress={() => onSelectMood(option.key)}
            />
          );
        })}
      </View>
      <Text style={styles.moodDescription}>{description}</Text>
      {canApply ? (
        <View style={styles.moodAdaptCard}>
          <Text style={styles.moodAdaptText}>
            Adapter la couleur du site a cette ambiance ?
          </Text>
          <Button
            title="Appliquer au site"
            onPress={() => setAccentKey(selectedMood)}
            variant="primary"
            style={styles.moodAdaptButton}
          />
        </View>
      ) : null}
    </>
  );
}
