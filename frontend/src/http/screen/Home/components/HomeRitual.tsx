import { Text, View } from 'react-native';
import { styles } from '../styles';
import { ritualHint, ritualPrompt } from '../utils/ritual';
import { HomeSectionHeader } from './HomeSectionHeader';

/**
 * Daily ritual prompt section.
 */
export function HomeRitual() {
  return (
    <View style={styles.section}>
      <HomeSectionHeader
        title="Rituel du jour"
        hint="Une question douce pour lancer l'écriture."
      />
      <View style={styles.ritualCard}>
        <Text style={styles.ritualPrompt}>{ritualPrompt}</Text>
        <Text style={styles.ritualHint}>{ritualHint}</Text>
      </View>
    </View>
  );
}
