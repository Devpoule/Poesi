import { Animated } from 'react-native';
import { Screen } from '../../../components/Screen';
import { useTheme } from '../../../../support/theme/tokens';
import { useRevealAnimation } from '../hooks/useRevealAnimation';
import { useWriteContent } from '../hooks/useWriteContent';
import { useWriteDraft } from '../hooks/useWriteDraft';
import { useWriteMoodTheme } from '../hooks/useWriteMoodTheme';
import { useStyles } from '../styles';
import { WriteBackdrop } from './WriteBackdrop';
import { WriteEditorCard } from './WriteEditorCard';
import { WriteHeader } from './WriteHeader';
import { getMoodLore } from '../utils/moodLore';

/**
 * Full write experience for authenticated users.
 */
export function WriteEditor() {
  const styles = useStyles();
  const { theme: appTheme, accentKey } = useTheme();
  const { reveals, revealStyle } = useRevealAnimation(2);
  const { draftTooltip, isDraftActive, handleDraftToggle, handleSave, markDirty } =
    useWriteDraft();
  const { title, body, handleTitleChange, handleBodyChange } = useWriteContent(markDirty);
  const mood = accentKey ?? 'neutre';
  const theme = useWriteMoodTheme(mood);
  const idleBorderColor = theme.isNeutral ? appTheme.colors.border : theme.moodAccent;
  const moodLore = getMoodLore(mood);
  const moodDescription = moodLore?.description ?? '';

  return (
    <Screen scroll contentStyle={styles.page}>
      <WriteBackdrop
        backdropStrong={theme.moodBackdropStrong}
        backdropSoft={theme.moodBackdropSoft}
        backdropRing={theme.moodBackdropRing}
      />

      <Animated.View style={revealStyle(reveals[0])}>
        <WriteHeader
          moodAccent={theme.moodAccent}
          subtitle={moodDescription}
          draftTooltip={draftTooltip}
          isDraftActive={isDraftActive}
          onDraftToggle={handleDraftToggle}
        />
      </Animated.View>

      <Animated.View
        style={[
          styles.card,
          styles.editorCard,
          { borderColor: theme.moodAccent, backgroundColor: theme.moodSurfaceLight },
          revealStyle(reveals[1]),
        ]}
      >
        <WriteEditorCard
          title={title}
          body={body}
          focusBorderColor={theme.focusBorderColor}
          idleBorderColor={idleBorderColor}
          editorFieldBackground={theme.editorFieldBackground}
          moodTextColor={theme.moodTextColor}
          primaryColor={theme.primaryColor}
          primaryHoverColor={theme.primaryHoverColor}
          primaryTextColor={theme.primaryTextColor}
          onTitleChange={handleTitleChange}
          onBodyChange={handleBodyChange}
          onSave={handleSave}
        />
      </Animated.View>
    </Screen>
  );
}
