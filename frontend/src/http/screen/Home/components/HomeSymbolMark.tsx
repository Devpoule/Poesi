import { View } from 'react-native';
import { styles } from '../styles';
import type { SymbolVariant } from '../utils/symbols';

type HomeSymbolMarkProps = {
  variant: SymbolVariant;
  color: string;
};

/**
 * Renders a tiny symbol mark used in the categories grid.
 */
export function HomeSymbolMark({ variant, color }: HomeSymbolMarkProps) {
  if (variant === 'wings') {
    return <View style={[styles.symbolWings, { borderColor: color }]} />;
  }

  if (variant === 'halo') {
    return <View style={[styles.symbolHalo, { borderColor: color }]} />;
  }

  if (variant === 'horizon') {
    return <View style={[styles.symbolHorizon, { backgroundColor: color }]} />;
  }

  if (variant === 'meteor') {
    return (
      <View style={styles.symbolMeteor}>
        <View style={[styles.symbolMeteorTrail, { backgroundColor: color }]} />
        <View style={[styles.symbolMeteorCore, { backgroundColor: color }]} />
      </View>
    );
  }

  return <View style={[styles.symbolWhirl, { borderColor: color }]} />;
}
