import { View } from 'react-native';
import { useStyles } from '../styles';

type WriteBackdropProps = {
  backdropStrong: string;
  backdropSoft: string;
  backdropRing: string;
};

/**
 * Decorative background shapes tinted to the selected mood.
 */
export function WriteBackdrop({
  backdropStrong,
  backdropSoft,
  backdropRing,
}: WriteBackdropProps) {
  const styles = useStyles();
  return (
    <View style={styles.moodBackdrop}>
      <View style={[styles.moodVeil, { backgroundColor: backdropStrong }]} />
      <View style={[styles.moodOrbPrimary, { backgroundColor: backdropSoft }]} />
      <View style={[styles.moodOrbSecondary, { backgroundColor: backdropSoft }]} />
      <View style={[styles.moodRing, { borderColor: backdropRing }]} />
    </View>
  );
}
