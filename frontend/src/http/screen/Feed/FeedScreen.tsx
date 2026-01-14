import { useMemo, useRef, useEffect, useState } from 'react';
import {
  ActivityIndicator,
  Animated,
  FlatList,
  Platform,
  Pressable,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { useRouter } from 'expo-router';
import type { Poem } from '../../../domain/poem/model/Poem';
import { useAuth } from '../../../bootstrap/AuthProvider';
import { Screen } from '../../components/Screen';
import { normalizeMoodKey, moodOptions, resolveMood } from '../../../support/theme/moods';
import { ThemeColors, spacing, typography, useTheme } from '../../../support/theme/tokens';
import { useFeedViewModel } from './FeedViewModel';

type FeedScreenProps = {
  title?: string;
  subtitle?: string;
  ctaLabel?: string;
};

type PoemCardProps = {
  poem: Poem;
  index: number;
};

type FilterOption = {
  key: string;
  label: string;
};

const filterOptions: FilterOption[] = [
  { key: 'all', label: 'Tous' },
  ...moodOptions.map((mood) => ({
    key: mood.key,
    label: mood.label,
  })),
];
const useNativeDriver = Platform.OS !== 'web';
const cardShadowStyle = Platform.select({
  web: { boxShadow: '0px 6px 12px rgba(0, 0, 0, 0.08)' } as any,
  default: {
    shadowColor: '#000000',
    shadowOpacity: 0.08,
    shadowRadius: 12,
    shadowOffset: { width: 0, height: 6 },
    elevation: 2,
  },
}) as any;

function formatPoemDate(value?: string | null) {
  if (!value) {
    return 'Date inconnue';
  }

  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    return 'Date inconnue';
  }

  return date.toLocaleDateString('fr-FR', { month: 'short', day: 'numeric' });
}

function formatStatus(value?: string | null) {
  if (!value) {
    return 'En attente';
  }

  const cleaned = value.replace(/[_-]/g, ' ');
  return cleaned.charAt(0).toUpperCase() + cleaned.slice(1);
}

function FilterChip({
  label,
  active,
  onPress,
  styles,
}: {
  label: string;
  active: boolean;
  onPress: () => void;
  styles: ReturnType<typeof createStyles>;
}) {
  return (
    <Pressable
      onPress={onPress}
      style={({ pressed }) => [
        styles.filterChip,
        active && styles.filterChipActive,
        pressed && styles.filterChipPressed,
      ]}
    >
      <Text style={[styles.filterChipText, active && styles.filterChipTextActive]}>
        {label}
      </Text>
    </Pressable>
  );
}

function PoemCard({ poem, index }: PoemCardProps) {
  const reveal = useRef(new Animated.Value(0)).current;
  const mood = resolveMood(poem.moodColor);
  const author = poem.user?.pseudo ?? 'Anonyme';
  const dateLabel = formatPoemDate(poem.publishedAt ?? poem.createdAt);
  const statusLabel = formatStatus(poem.status);

  useEffect(() => {
    Animated.timing(reveal, {
      toValue: 1,
      duration: 320,
      delay: index * 80,
      useNativeDriver,
    }).start();
  }, [index, reveal]);

  return (
    <Animated.View
      style={[
        styles.card,
        {
          opacity: reveal,
          transform: [
            {
              translateY: reveal.interpolate({
                inputRange: [0, 1],
                outputRange: [12, 0],
              }),
            },
          ],
        },
      ]}
    >
      <View style={styles.cardHeader}>
        <Text style={styles.cardTitle}>{poem.title || 'Sans titre'}</Text>
        <View style={[styles.moodBadge, { borderColor: mood.color }]}>
          <View style={[styles.moodDot, { backgroundColor: mood.color }]} />
          <Text style={styles.moodLabel}>{mood.label}</Text>
        </View>
      </View>
      <Text style={styles.cardAuthor}>par {author}</Text>
      <View style={styles.cardMetaRow}>
        <Text style={styles.cardMeta}>{dateLabel}</Text>
      </View>
      <View style={styles.badgeRow}>
        <View style={styles.badge}>
          <Text style={styles.badgeText}>{statusLabel}</Text>
        </View>
        {poem.symbolType ? (
          <View style={styles.badge}>
            <Text style={styles.badgeText}>{poem.symbolType}</Text>
          </View>
        ) : null}
      </View>
    </Animated.View>
  );
}

export default function FeedScreen({
  title = 'Flux',
  subtitle = "Les textes en cours d'envol.",
  ctaLabel = 'Ecrire',
}: FeedScreenProps) {
  const { theme } = useTheme();
  const styles = useMemo(() => createStyles(theme.colors), [theme.colors]);
  const router = useRouter();
  const { tokens } = useAuth();
  const { items, isLoading, error, reload } = useFeedViewModel();
  const [selectedMood, setSelectedMood] = useState('all');
  const writeLabel = tokens ? ctaLabel : 'Se connecter';

  const activeMood = useMemo(
    () => (selectedMood === 'all' ? null : moodOptions.find((m) => m.key === selectedMood) ?? null),
    [selectedMood]
  );

  const filteredItems = useMemo(() => {
    if (selectedMood === 'all') {
      return items;
    }
    return items.filter((poem) => normalizeMoodKey(poem.moodColor) === selectedMood);
  }, [items, selectedMood]);

  const handleWrite = () => {
    router.push(tokens ? '/(tabs)/write' : '/(auth)/login');
  };

  return (
    <Screen>
      <View style={styles.container}>
        <View
          style={[
            styles.moodBackdrop,
            activeMood && { backgroundColor: hexToRgba(activeMood.color, 0.08) },
          ]}
        />
        <View style={styles.header}>
          <Text style={styles.title}>{title}</Text>
          <Text style={styles.subtitle}>{subtitle}</Text>
          <Pressable style={styles.ctaButton} onPress={handleWrite}>
            <Text style={styles.ctaText}>{writeLabel}</Text>
          </Pressable>
        </View>

        <View style={styles.filterRow}>
          {filterOptions.map((option) => (
            <FilterChip
              key={option.key}
              label={option.label}
              active={selectedMood === option.key}
              onPress={() => setSelectedMood(option.key)}
              styles={styles}
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
            <Pressable style={styles.retryButton} onPress={reload}>
              <Text style={styles.retryText}>Reessayer</Text>
            </Pressable>
          </View>
        ) : null}

        {!isLoading && !error ? (
          <FlatList
            data={filteredItems}
            keyExtractor={(item) => item.id.toString()}
            renderItem={({ item, index }) => <PoemCard poem={item} index={index} />}
            ItemSeparatorComponent={() => <View style={styles.separator} />}
            ListEmptyComponent={
              <View style={styles.emptyState}>
                <Text style={styles.emptyTitle}>Aucun texte pour l'instant.</Text>
                <Text style={styles.emptyText}>
                  Pose les premiers mots et lance l'envol.
                </Text>
                <Pressable style={styles.emptyButton} onPress={handleWrite}>
                  <Text style={styles.emptyButtonText}>Ecrire un texte</Text>
                </Pressable>
              </View>
            }
            contentContainerStyle={styles.listContent}
            showsVerticalScrollIndicator={false}
          />
        ) : null}
      </View>
    </Screen>
  );
}

function createStyles(colors: ThemeColors) {
  return StyleSheet.create({
    container: {
      flex: 1,
      paddingHorizontal: spacing.md,
    },
    moodBackdrop: {
      ...StyleSheet.absoluteFillObject,
      zIndex: -1,
    },
    header: {
      marginBottom: spacing.md,
    },
    title: {
      fontSize: typography.display,
      fontFamily: typography.fontFamily,
      color: colors.textPrimary,
    },
    subtitle: {
      fontSize: typography.body,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
      marginTop: spacing.xs,
    },
    ctaButton: {
      alignSelf: 'flex-start',
      marginTop: spacing.sm,
      backgroundColor: colors.surface,
      borderRadius: 999,
      paddingVertical: spacing.xs,
      paddingHorizontal: spacing.md,
      borderWidth: 1,
      borderColor: colors.border,
    },
    ctaText: {
      color: colors.textPrimary,
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
    },
    filterRow: {
      flexDirection: 'row',
      flexWrap: 'wrap',
      marginBottom: spacing.md,
    },
    filterChip: {
      paddingVertical: spacing.xs,
      paddingHorizontal: spacing.sm,
      borderRadius: 999,
      borderWidth: 1,
      borderColor: colors.border,
      backgroundColor: colors.surface,
      marginRight: spacing.sm,
      marginBottom: spacing.sm,
    },
    filterChipActive: {
      borderColor: colors.accent,
      backgroundColor: colors.accentSoft,
    },
    filterChipPressed: {
      opacity: 0.75,
    },
    filterChipText: {
      fontSize: typography.caption,
      color: colors.textSecondary,
      fontFamily: typography.fontFamily,
    },
    filterChipTextActive: {
      color: colors.textPrimary,
    },
    listContent: {
      paddingTop: spacing.md,
      paddingHorizontal: spacing.sm,
      paddingBottom: spacing.xxl,
    },
    separator: {
      height: spacing.md,
    },
    card: {
      backgroundColor: colors.surface,
      borderRadius: 20,
      padding: spacing.md,
      borderWidth: 1,
      borderColor: colors.border,
      ...cardShadowStyle,
    },
    cardHeader: {
      flexDirection: 'row',
      alignItems: 'center',
    },
    cardTitle: {
      flex: 1,
      fontSize: typography.body,
      fontFamily: typography.fontFamily,
      color: colors.textPrimary,
      marginRight: spacing.sm,
    },
    moodBadge: {
      flexDirection: 'row',
      alignItems: 'center',
      paddingVertical: 4,
      paddingHorizontal: spacing.sm,
      borderRadius: 999,
      borderWidth: 1,
      backgroundColor: colors.surfaceElevated,
    },
    moodDot: {
      width: 8,
      height: 8,
      borderRadius: 4,
      marginRight: spacing.xs,
    },
    moodLabel: {
      fontSize: typography.small,
      fontFamily: typography.fontFamily,
      color: colors.textPrimary,
    },
    cardAuthor: {
      marginTop: spacing.xs,
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
    },
    cardMetaRow: {
      flexDirection: 'row',
      flexWrap: 'wrap',
      marginTop: spacing.xs,
    },
    cardMeta: {
      fontSize: typography.small,
      fontFamily: typography.fontFamily,
      color: colors.textMuted,
      marginRight: spacing.sm,
      marginTop: spacing.xs,
    },
    badgeRow: {
      flexDirection: 'row',
      flexWrap: 'wrap',
      marginTop: spacing.sm,
    },
    badge: {
      paddingVertical: 4,
      paddingHorizontal: spacing.sm,
      borderRadius: 999,
      backgroundColor: colors.surfaceMuted,
      marginRight: spacing.sm,
      marginBottom: spacing.xs,
    },
    badgeText: {
      fontSize: typography.small,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
    },
    centered: {
      alignItems: 'center',
      justifyContent: 'center',
      paddingVertical: spacing.lg,
    },
    loadingText: {
      marginTop: spacing.sm,
      fontFamily: typography.fontFamily,
      color: colors.textMuted,
    },
    errorBox: {
      backgroundColor: colors.surface,
      borderRadius: 16,
      padding: spacing.md,
      borderWidth: 1,
      borderColor: colors.border,
      marginBottom: spacing.lg,
    },
    errorText: {
      color: colors.danger,
      fontFamily: typography.fontFamily,
      marginBottom: spacing.sm,
    },
    retryButton: {
      alignSelf: 'flex-start',
      paddingVertical: spacing.xs,
      paddingHorizontal: spacing.sm,
      borderRadius: 999,
      borderWidth: 1,
      borderColor: colors.border,
    },
    retryText: {
      color: colors.textPrimary,
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
    },
    emptyState: {
      alignItems: 'center',
      paddingVertical: spacing.xxl,
    },
    emptyTitle: {
      fontSize: typography.body,
      fontFamily: typography.fontFamily,
      color: colors.textPrimary,
      marginBottom: spacing.xs,
    },
    emptyText: {
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textMuted,
      textAlign: 'center',
      marginBottom: spacing.md,
    },
    emptyButton: {
      backgroundColor: colors.surface,
      borderWidth: 1,
      borderColor: colors.border,
      paddingVertical: spacing.xs,
      paddingHorizontal: spacing.lg,
      borderRadius: 999,
    },
    emptyButtonText: {
      color: colors.textPrimary,
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
    },
  });
}

function hexToRgba(hex: string, alpha: number) {
  if (!hex || typeof hex !== 'string') {
    return 'transparent';
  }
  const normalized = hex.replace('#', '');
  if (normalized.length !== 6) {
    return 'transparent';
  }
  const r = parseInt(normalized.slice(0, 2), 16);
  const g = parseInt(normalized.slice(2, 4), 16);
  const b = parseInt(normalized.slice(4, 6), 16);
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}
