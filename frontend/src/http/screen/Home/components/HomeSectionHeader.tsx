import { Pressable, Text, View } from 'react-native';
import { styles } from '../styles';

type HomeSectionHeaderProps = {
  title: string;
  hint: string;
  actionLabel?: string;
  onAction?: () => void;
};

/**
 * Renders a section header with a hint and optional action.
 */
export function HomeSectionHeader({
  title,
  hint,
  actionLabel,
  onAction,
}: HomeSectionHeaderProps) {
  return (
    <View style={styles.sectionHeader}>
      <Text style={styles.sectionTitle}>{title}</Text>
      <Text style={styles.sectionHint}>{hint}</Text>
      {actionLabel && onAction ? (
        <Pressable style={styles.sectionAction} onPress={onAction}>
          <Text style={styles.sectionActionText}>{actionLabel}</Text>
        </Pressable>
      ) : null}
    </View>
  );
}
