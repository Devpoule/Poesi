import { Pressable, Text } from 'react-native';
import { useStyles } from '../styles';

type HomeFilterChipProps = {
  label: string;
  active: boolean;
  onPress: () => void;
};

/**
 * Filter chip used in the recent poems section.
 */
export function HomeFilterChip({ label, active, onPress }: HomeFilterChipProps) {
  const styles = useStyles();
  return (
    <Pressable
      onPress={onPress}
      style={({ pressed }) => [
        styles.filterChip,
        active && styles.filterChipActive,
        pressed && styles.filterChipPressed,
      ]}
    >
      <Text style={[styles.filterChipText, active && styles.filterChipTextActive]}>
        {label}
      </Text>
    </Pressable>
  );
}
