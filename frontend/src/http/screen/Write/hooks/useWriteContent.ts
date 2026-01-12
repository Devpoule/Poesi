import { useCallback, useState } from 'react';

/**
 * Stores title/body text and notifies when content changes.
 */
export function useWriteContent(onDirty?: () => void) {
  const [title, setTitle] = useState('');
  const [body, setBody] = useState('');

  const handleTitleChange = useCallback(
    (value: string) => {
      setTitle(value);
      onDirty?.();
    },
    [onDirty]
  );

  const handleBodyChange = useCallback(
    (value: string) => {
      setBody(value);
      onDirty?.();
    },
    [onDirty]
  );

  return {
    title,
    body,
    handleTitleChange,
    handleBodyChange,
  };
}
