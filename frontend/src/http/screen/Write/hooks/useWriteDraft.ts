import { useMemo, useState } from 'react';

const DEFAULT_NOTE = 'Autosauvegarde active';

const buildDraftTooltip = (note: string) => {
  const normalizedNote = note.toLowerCase();
  if (normalizedNote.startsWith('brouillon')) {
    return `Brouillon - ${normalizedNote.replace(/^brouillon\\s*/u, '')}.`;
  }
  return `Brouillon - ${normalizedNote}.`;
};

/**
 * Manages draft status messaging and toggling.
 */
export function useWriteDraft() {
  const [saveNote, setSaveNote] = useState(DEFAULT_NOTE);
  const [isDraftActive, setIsDraftActive] = useState(true);

  const statusNote = isDraftActive ? saveNote : 'Sauvegarde désactivée';
  const draftTooltip = useMemo(() => buildDraftTooltip(statusNote), [statusNote]);

  const handleDraftToggle = () => {
    setIsDraftActive((prev) => {
      const next = !prev;
      if (next) {
        setSaveNote(DEFAULT_NOTE);
      }
      return next;
    });
  };

  const markDirty = () => {
    if (isDraftActive) {
      setSaveNote('Modifications en cours');
    }
  };

  const handleSave = () => {
    setSaveNote('Brouillon enregistré');
  };

  return {
    saveNote,
    isDraftActive,
    draftTooltip,
    handleDraftToggle,
    markDirty,
    handleSave,
  };
}
