import { Animated } from 'react-native';
import { Screen } from '../../../components/Screen';
import { useTheme } from '../../../../support/theme/tokens';
import { useRevealAnimation } from '../hooks/useRevealAnimation';
import { useWriteContent } from '../hooks/useWriteContent';
import { useWriteDraft } from '../hooks/useWriteDraft';
import { useWriteMoodTheme } from '../hooks/useWriteMoodTheme';
import { usePoemSave } from '../hooks/usePoemSave';
import { useStyles } from '../styles';
import { WriteBackdrop } from './WriteBackdrop';
import { WriteEditorCard } from './WriteEditorCard';
import { WriteHeader } from './WriteHeader';
import { getMoodLore } from '../utils/moodLore';
import { MoodPaletteSection } from '../../../components/MoodPaletteSection';

/**
 * Full write experience for authenticated users.
 */
export function WriteEditor() {
  const styles = useStyles();
  const { theme: appTheme, accentKey, setAccentKey } = useTheme();
  const { reveals, revealStyle } = useRevealAnimation(2);
  const { draftTooltip, isDraftActive, handleDraftToggle, handleSave, markDirty, setSaveNote } =
    useWriteDraft();
  const { title, body, handleTitleChange, handleBodyChange } = useWriteContent(markDirty);
  const mood = accentKey ?? 'neutre';
  const theme = useWriteMoodTheme(mood);
  const { save, isSaving, error: saveError } = usePoemSave();
  const idleBorderColor = theme.isNeutral ? appTheme.colors.border : theme.moodAccent;
  const moodLore = getMoodLore(mood);
  const moodDescription = moodLore?.description ?? '';

  const handleSavePoem = async () => {
    if (!title.trim()) {
      setSaveNote('Titre requis');
      return;
    }
    if (!body.trim()) {
      setSaveNote('Contenu requis');
      return;
    }
    try {
      await save({
        title: title || 'Sans titre',
        content: body,
        moodColor: mood,
      });
      handleSave('Brouillon enregistre');
    } catch {
      setSaveNote('Erreur lors de la sauvegarde');
    }
  };

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
          onSave={handleSavePoem}
          isSaving={isSaving}
          saveError={saveError ?? undefined}
        />
      </Animated.View>

      <Animated.View style={revealStyle(reveals[1])}>
        <MoodPaletteSection
          title="Ambiance du texte"
          hint="Choisis la couleur qui reflete ton etat d'esprit."
          selectedKey={mood}
          onSelect={(key) => setAccentKey(key === 'neutre' ? null : key)}
          showDescription
          columns={2}
        />
      </Animated.View>
    </Screen>
  );
}
