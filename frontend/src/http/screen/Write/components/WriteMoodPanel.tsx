import { Text, View } from 'react-native';
import { moodOptions } from '../../../../support/theme/moods';
import { getMoodLore } from '../utils/moodLore';
import { styles } from '../styles';
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
    </>
  );
}
