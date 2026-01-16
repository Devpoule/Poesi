import { Platform, Pressable, Text, View } from 'react-native';
import { useStyles } from '../styles';
import { Button } from '../../../components/Button';

type WriteActionsProps = {
  primaryColor: string;
  primaryHoverColor: string;
  primaryTextColor: string;
  onSave: () => void;
  isSaving?: boolean;
  saveError?: string;
};

/**
 * Action row for saving and publishing drafts.
 */
export function WriteActions({
  primaryColor,
  primaryHoverColor,
  primaryTextColor,
  onSave,
  isSaving = false,
  saveError,
}: WriteActionsProps) {
  const styles = useStyles();
  const label = isSaving ? 'Sauvegarde...' : 'Sauvegarder';
  return (
    <View style={styles.actionRow}>
      {saveError ? (
        <Text style={[styles.primaryButtonText, { color: '#c84a3c', marginRight: 12 }]}>
          {saveError}
        </Text>
      ) : null}
      {Platform.OS === 'web' ? (
        <Button
          title={label}
          onPress={onSave}
          variant="primary"
          backgroundColor={primaryColor}
          hoverBackgroundColor={primaryHoverColor}
          textColor={primaryTextColor}
          disabled={isSaving}
        />
      ) : (
        <Pressable
          style={(state) => {
            const hovered = (state as { hovered?: boolean }).hovered;
            return [
              styles.primaryButton,
              { backgroundColor: primaryColor },
              hovered && styles.primaryButtonHover,
              hovered && { backgroundColor: primaryHoverColor },
              hovered && styles.buttonHover,
              isSaving && { opacity: 0.65 },
            ];
          }}
          onPress={onSave}
          disabled={isSaving}
        >
          <Text style={[styles.primaryButtonText, { color: primaryTextColor }]}>{label}</Text>
        </Pressable>
      )}
    </View>
  );
}
