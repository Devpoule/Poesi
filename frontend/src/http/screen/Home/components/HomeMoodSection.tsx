import { Pressable, Text, View } from 'react-native';
import { moodOptions } from '../../../../support/theme/moods';
import { useTheme } from '../../../../support/theme/tokens';
import { getMoodLore } from '../../Write/utils/moodLore';
import { useStyles } from '../styles';
import { HomeSectionHeader } from './HomeSectionHeader';

type HomeMoodSectionProps = {
  selectedKey?: string;
  onSelect?: (key: string) => void;
  title?: string;
  hint?: string;
  showDescription?: boolean;
  columns?: number;
};

/**
 * Displays the mood category pills.
 */
export function HomeMoodSection({
  selectedKey,
  onSelect,
  title = 'Moods',
  hint = "Choisis la couleur d'ambiance qui reflète ton état d'esprit.",
  showDescription = false,
  columns = 0,
}: HomeMoodSectionProps) {
  const styles = useStyles();
  const { accentKey, setAccentKey } = useTheme();
  const activeKey = selectedKey ?? accentKey ?? 'neutre';
  const activeMood = moodOptions.find((mood) => mood.key === activeKey) ?? moodOptions[0];
  const moodLore = getMoodLore(activeKey);
  const description = moodLore?.description ?? 'Perception en attente.';

  const handleSelect = (key: string) => {
    if (onSelect) {
      onSelect(key);
      return;
    }
    setAccentKey(key === 'neutre' ? null : key);
  };

  return (
    <View style={styles.section}>
      <HomeSectionHeader title={title} hint={hint} />
      <View style={styles.moodGrid}>
        {moodOptions.map((mood) => (
          <Pressable
            key={mood.key}
            style={[
              styles.moodPill,
              columns === 2 && styles.moodPillHalf,
              activeKey === mood.key && styles.moodPillActive,
            ]}
            onPress={() => handleSelect(mood.key)}
          >
            <View style={[styles.moodDot, { backgroundColor: mood.color }]} />
            <Text
              style={[
                styles.moodLabel,
                activeKey === mood.key && styles.moodLabelActive,
              ]}
            >
              {mood.label}
            </Text>
          </Pressable>
        ))}
      </View>
      {showDescription ? (
        <View style={styles.moodDescriptionCard}>
          <Text style={styles.moodDescriptionTitle}>{activeMood.label}</Text>
          <Text style={styles.moodDescriptionText}>{description}</Text>
        </View>
      ) : null}
    </View>
  );
}
