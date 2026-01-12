import { useCallback, useEffect, useState } from 'react';
import type { Poem } from '../../../../domain/poem/model/Poem';
import { fetchFeed } from '../../../../domain/poem/usecase/FetchFeed';
import { poemRepository } from '../../../../bootstrap/repositories';

/**
 * Loads a recent feed of poems for the home screen.
 */
export function useRecentPoems(limit = 12) {
  const [items, setItems] = useState<Poem[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const load = useCallback(async () => {
    setError(null);
    setIsLoading(true);
    try {
      const page = await fetchFeed(poemRepository, { limit });
      setItems(page.items);
    } catch (err) {
      setItems([]);
      setError(err instanceof Error ? err.message : 'Impossible de charger le feed.');
    } finally {
      setIsLoading(false);
    }
  }, [limit]);

  useEffect(() => {
    load();
  }, [load]);

  return {
    items,
    isLoading,
    error,
    reload: load,
  };
}
