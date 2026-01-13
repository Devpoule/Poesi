import { TextInput } from 'react-native';
import { useTheme } from '../../../../support/theme/tokens';
import { useWriteFocus } from '../hooks/useWriteFocus';
import { useStyles } from '../styles';

type WriteFieldsProps = {
  title: string;
  body: string;
  focusBorderColor: string;
  idleBorderColor: string;
  editorFieldBackground: string;
  moodTextColor: string;
  onTitleChange: (value: string) => void;
  onBodyChange: (value: string) => void;
};

/**
 * Title and body inputs with mood-aware styling.
 */
export function WriteFields({
  title,
  body,
  focusBorderColor,
  idleBorderColor,
  editorFieldBackground,
  moodTextColor,
  onTitleChange,
  onBodyChange,
}: WriteFieldsProps) {
  const styles = useStyles();
  const { theme } = useTheme();
  const {
    isTitleFocused,
    isBodyFocused,
    handleTitleFocus,
    handleTitleBlur,
    handleBodyFocus,
    handleBodyBlur,
  } = useWriteFocus();

  return (
    <>
      <TextInput
        value={title}
        onChangeText={onTitleChange}
        onFocus={handleTitleFocus}
        onBlur={handleTitleBlur}
        selectionColor={focusBorderColor}
        placeholder="Un nom pour ton poeme"
        placeholderTextColor={theme.colors.textMuted}
        style={[
          styles.input,
          {
            borderBottomColor: isTitleFocused ? focusBorderColor : idleBorderColor,
            color: moodTextColor,
          },
        ]}
      />
      <TextInput
        value={body}
        onChangeText={onBodyChange}
        onFocus={handleBodyFocus}
        onBlur={handleBodyBlur}
        selectionColor={focusBorderColor}
        placeholder="Ecrire ici, en douceur."
        placeholderTextColor={theme.colors.textMuted}
        style={[
          styles.textArea,
          { backgroundColor: editorFieldBackground, color: moodTextColor },
          { borderColor: isBodyFocused ? focusBorderColor : idleBorderColor },
        ]}
        multiline
        textAlignVertical="top"
      />
    </>
  );
}
