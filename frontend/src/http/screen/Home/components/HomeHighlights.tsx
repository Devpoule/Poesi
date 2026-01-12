import { Text, View } from 'react-native';
import { highlightItems } from '../utils/highlights';
import { styles } from '../styles';

/**
 * Renders the trio of value highlights under the hero.
 */
export function HomeHighlights() {
  return (
    <View style={styles.highlightRow}>
      {highlightItems.map((item) => (
        <View key={item.key} style={styles.highlightCard}>
          <Text style={styles.highlightTitle}>{item.title}</Text>
          <Text style={styles.highlightText}>{item.description}</Text>
        </View>
      ))}
    </View>
  );
}
