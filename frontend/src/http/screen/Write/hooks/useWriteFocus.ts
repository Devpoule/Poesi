import { useCallback, useState } from 'react';

/**
 * Tracks focus state for title and body inputs.
 */
export function useWriteFocus() {
  const [isTitleFocused, setIsTitleFocused] = useState(false);
  const [isBodyFocused, setIsBodyFocused] = useState(false);

  const handleTitleFocus = useCallback(() => {
    setIsTitleFocused(true);
  }, []);

  const handleTitleBlur = useCallback(() => {
    setIsTitleFocused(false);
  }, []);

  const handleBodyFocus = useCallback(() => {
    setIsBodyFocused(true);
  }, []);

  const handleBodyBlur = useCallback(() => {
    setIsBodyFocused(false);
  }, []);

  return {
    isTitleFocused,
    isBodyFocused,
    handleTitleFocus,
    handleTitleBlur,
    handleBodyFocus,
    handleBodyBlur,
  };
}
