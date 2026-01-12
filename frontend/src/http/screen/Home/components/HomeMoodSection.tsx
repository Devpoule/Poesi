import { Text, View } from 'react-native';
import { moodOptions } from '../../../../support/theme/moods';
import { styles } from '../styles';
import { HomeSectionHeader } from './HomeSectionHeader';

/**
 * Displays the mood category pills.
 */
export function HomeMoodSection() {
  return (
    <View style={styles.section}>
      <HomeSectionHeader
        title="Moods"
        hint="Choisis une couleur d'écriture ou laisse le silence décider."
      />
      <View style={styles.moodGrid}>
        {moodOptions.map((mood) => (
          <View key={mood.key} style={styles.moodPill}>
            <View style={[styles.moodDot, { backgroundColor: mood.color }]} />
            <Text style={styles.moodLabel}>{mood.label}</Text>
          </View>
        ))}
      </View>
    </View>
  );
}
