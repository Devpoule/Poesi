import { Text, View } from 'react-native';
import { useStyles } from '../styles';
import { useSymbolItems } from '../utils/symbols';
import { HomeSectionHeader } from './HomeSectionHeader';
import { HomeSymbolMark } from './HomeSymbolMark';

/**
 * Displays the symbol category grid.
 */
export function HomeSymbolSection() {
  const styles = useStyles();
  const symbolItems = useSymbolItems();
  return (
    <View style={styles.section}>
      <HomeSectionHeader
        title="Symboles"
        hint="Des signes discrets pour guider la lecture."
      />
      <View style={styles.symbolGrid}>
        {symbolItems.map((symbol) => (
          <View key={symbol.key} style={styles.symbolCard}>
            <HomeSymbolMark variant={symbol.variant} color={symbol.color} />
            <Text style={styles.symbolLabel}>{symbol.label}</Text>
          </View>
        ))}
      </View>
    </View>
  );
}
