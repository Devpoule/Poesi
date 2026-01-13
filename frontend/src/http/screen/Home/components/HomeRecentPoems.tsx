import { useMemo, useState } from 'react';
import { ActivityIndicator, Pressable, Text, View } from 'react-native';
import type { Poem } from '../../../../domain/poem/model/Poem';
import { normalizeMoodKey, moodOptions } from '../../../../support/theme/moods';
import { useTheme } from '../../../../support/theme/tokens';
import { HomeFilterChip } from './HomeFilterChip';
import { HomePoemCard } from './HomePoemCard';
import { HomeSectionHeader } from './HomeSectionHeader';
import { useStyles } from '../styles';

type HomeRecentPoemsProps = {
  items: Poem[];
  isLoading: boolean;
  error: string | null;
  onReload: () => void;
  onWrite: () => void;
  onViewAll: () => void;
};

const filterOptions = [
  { key: 'all', label: 'Tous' },
  ...moodOptions.map((mood) => ({ key: mood.key, label: mood.label })),
];

const maxItems = 6;

/**
 * Recent poems section with filters, status, and empty states.
 */
export function HomeRecentPoems({
  items,
  isLoading,
  error,
  onReload,
  onWrite,
  onViewAll,
}: HomeRecentPoemsProps) {
  const { theme } = useTheme();
  const styles = useStyles();
  const [selectedMood, setSelectedMood] = useState('all');

  const filteredItems = useMemo(() => {
    if (selectedMood === 'all') {
      return items;
    }
    return items.filter((poem) => normalizeMoodKey(poem.moodColor) === selectedMood);
  }, [items, selectedMood]);

  const visibleItems = filteredItems.slice(0, maxItems);

  return (
    <View style={styles.section}>
      <HomeSectionHeader
        title="Écrits récents"
        hint="Partager avec la communauté."
        actionLabel="Voir tout"
        onAction={onViewAll}
      />

      <View style={styles.filterRow}>
        {filterOptions.map((option) => (
          <HomeFilterChip
            key={option.key}
            label={option.label}
            active={selectedMood === option.key}
            onPress={() => setSelectedMood(option.key)}
          />
        ))}
      </View>

      {isLoading ? (
        <View style={styles.centered}>
          <ActivityIndicator color={theme.colors.textSecondary} />
          <Text style={styles.loadingText}>Chargement...</Text>
        </View>
      ) : null}

      {error ? (
        <View style={styles.errorBox}>
          <Text style={styles.errorText}>{error}</Text>
          <Pressable style={styles.retryButton} onPress={onReload}>
            <Text style={styles.retryText}>Réessayer</Text>
          </Pressable>
        </View>
      ) : null}

      {!isLoading && !error ? (
        <View style={styles.poemList}>
          {visibleItems.length ? (
            visibleItems.map((poem, index) => (
              <View key={poem.id} style={index ? styles.poemSeparator : null}>
                <HomePoemCard poem={poem} index={index} />
              </View>
            ))
          ) : (
            <View style={styles.emptyState}>
              <Text style={styles.emptyTitle}>Aucun texte pour l'instant.</Text>
              <Text style={styles.emptyText}>
                Pose les premiers mots et lance l'envol.
              </Text>
              <Pressable style={styles.emptyButton} onPress={onWrite}>
                <Text style={styles.emptyButtonText}>Écrire un texte</Text>
              </Pressable>
            </View>
          )}
        </View>
      ) : null}
    </View>
  );
}
