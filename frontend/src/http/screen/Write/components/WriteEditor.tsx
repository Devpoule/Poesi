import { useState } from 'react';
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
import { WriteMoodPanel } from './WriteMoodPanel';

/**
 * Full write experience for authenticated users.
 */
export function WriteEditor() {
  const styles = useStyles();
  const { theme: appTheme } = useTheme();
  const [mood, setMood] = useState('neutre');
  const { reveals, revealStyle } = useRevealAnimation(3);
  const { draftTooltip, isDraftActive, handleDraftToggle, handleSave, markDirty } =
    useWriteDraft();
  const { title, body, handleTitleChange, handleBodyChange } = useWriteContent(markDirty);
  const theme = useWriteMoodTheme(mood);
  const idleBorderColor = theme.isNeutral ? appTheme.colors.border : theme.moodAccent;

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
          draftTooltip={draftTooltip}
          isDraftActive={isDraftActive}
          onDraftToggle={handleDraftToggle}
        />
      </Animated.View>

      <Animated.View
        style={[
          styles.card,
          styles.moodPanel,
          { borderColor: theme.moodAccent, backgroundColor: theme.moodSurfaceStrong },
          revealStyle(reveals[1]),
        ]}
      >
        <WriteMoodPanel
          selectedMood={mood}
          description={theme.moodDescription}
          onSelectMood={setMood}
        />
      </Animated.View>

      <Animated.View
        style={[
          styles.card,
          styles.editorCard,
          { borderColor: theme.moodAccent, backgroundColor: theme.moodSurfaceLight },
          revealStyle(reveals[2]),
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
