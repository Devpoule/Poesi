import { Platform, Pressable, Text, View } from 'react-native';
import { styles } from '../styles';
import { Button } from '../../../components/Button';

type WriteActionsProps = {
  primaryColor: string;
  primaryHoverColor: string;
  primaryTextColor: string;
  onSave: () => void;
};

/**
 * Action row for saving and publishing drafts.
 */
export function WriteActions({
  primaryColor,
  primaryHoverColor,
  primaryTextColor,
  onSave,
}: WriteActionsProps) {
  return (
    <View style={styles.actionRow}>
      {Platform.OS === 'web' ? (
        <>
          <Button title="Sauver" onPress={onSave} variant="secondary" />
          <Button
            title="Publier plus tard"
            onPress={onSave}
            variant="primary"
            backgroundColor={primaryColor}
            hoverBackgroundColor={primaryHoverColor}
            textColor={primaryTextColor}
          />
        </>
      ) : (
        <>
          <Pressable
            style={({ hovered }) => [
              styles.secondaryButton,
              hovered && styles.secondaryButtonHover,
              hovered && styles.buttonHover,
            ]}
            onPress={onSave}
          >
            <Text style={styles.secondaryButtonText}>Sauver</Text>
          </Pressable>
          <Pressable
            style={({ hovered }) => [
              styles.primaryButton,
              { backgroundColor: primaryColor },
              hovered && styles.primaryButtonHover,
              hovered && { backgroundColor: primaryHoverColor },
              hovered && styles.buttonHover,
            ]}
            onPress={onSave}
          >
            <Text style={[styles.primaryButtonText, { color: primaryTextColor }]}>Publier plus tard</Text>
          </Pressable>
        </>
      )}
    </View>
  );
}
