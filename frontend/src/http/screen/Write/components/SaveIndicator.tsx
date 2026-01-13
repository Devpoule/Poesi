import { useState } from 'react';
import { Pressable, Text, View } from 'react-native';
import { useTheme } from '../../../../support/theme/tokens';
import { useStyles } from '../styles';

type SaveIndicatorProps = {
  label: string;
  color: string;
  active: boolean;
  onToggle: () => void;
};

/**
 * Shows the draft state toggle with a tooltip on hover.
 */
export function SaveIndicator({ label, color, active, onToggle }: SaveIndicatorProps) {
  const styles = useStyles();
  const { theme } = useTheme();
  const [open, setOpen] = useState(false);
  const iconColor = active ? color : theme.colors.textMuted;

  return (
    <View style={styles.saveIndicator}>
      <Pressable
        onHoverIn={() => setOpen(true)}
        onHoverOut={() => setOpen(false)}
        onPress={() => {
          onToggle();
          setOpen((prev) => !prev);
        }}
        style={({ pressed }) => [
          styles.saveButton,
          !active && styles.saveButtonDisabled,
          { borderColor: iconColor },
          pressed && styles.saveButtonPressed,
        ]}
        accessibilityLabel="Brouillon"
        accessibilityHint={label}
      >
        <View style={[styles.saveIcon, { borderColor: iconColor }]}>
          <View style={[styles.saveTop, { backgroundColor: iconColor }]} />
          <View style={[styles.saveBottom, { borderColor: iconColor }]} />
          {!active ? <View style={[styles.saveSlash, { backgroundColor: iconColor }]} /> : null}
        </View>
      </Pressable>
      {open ? (
        <View style={styles.saveTooltip}>
          <Text style={styles.saveTooltipText}>{label}</Text>
        </View>
      ) : null}
    </View>
  );
}
