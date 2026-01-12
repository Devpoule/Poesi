import { WriteActions } from './WriteActions';
import { WriteFields } from './WriteFields';

type WriteEditorCardProps = {
  title: string;
  body: string;
  focusBorderColor: string;
  idleBorderColor: string;
  editorFieldBackground: string;
  moodTextColor: string;
  primaryColor: string;
  primaryHoverColor: string;
  primaryTextColor: string;
  onTitleChange: (value: string) => void;
  onBodyChange: (value: string) => void;
  onSave: () => void;
};

/**
 * Combines the write fields and action buttons within the editor card.
 */
export function WriteEditorCard({
  title,
  body,
  focusBorderColor,
  idleBorderColor,
  editorFieldBackground,
  moodTextColor,
  primaryColor,
  primaryHoverColor,
  primaryTextColor,
  onTitleChange,
  onBodyChange,
  onSave,
}: WriteEditorCardProps) {
  return (
    <>
      <WriteFields
        title={title}
        body={body}
        focusBorderColor={focusBorderColor}
        idleBorderColor={idleBorderColor}
        editorFieldBackground={editorFieldBackground}
        moodTextColor={moodTextColor}
        onTitleChange={onTitleChange}
        onBodyChange={onBodyChange}
      />
      <WriteActions
        primaryColor={primaryColor}
        primaryHoverColor={primaryHoverColor}
        primaryTextColor={primaryTextColor}
        onSave={onSave}
      />
    </>
  );
}
