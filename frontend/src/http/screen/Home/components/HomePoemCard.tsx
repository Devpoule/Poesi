import { useEffect, useRef } from 'react';
import { Animated, Platform, Text, View } from 'react-native';
import type { Poem } from '../../../../domain/poem/model/Poem';
import { resolveMood } from '../../../../support/theme/moods';
import { useStyles } from '../styles';
import { formatPoemDate, formatStatus } from '../utils/poemFormatting';

type HomePoemCardProps = {
  poem: Poem;
  index: number;
};

const useNativeDriver = Platform.OS !== 'web';

/**
 * Animated preview card for a recent poem.
 */
export function HomePoemCard({ poem, index }: HomePoemCardProps) {
  const styles = useStyles();
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
        styles.poemCard,
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
      <View style={styles.poemHeader}>
        <Text style={styles.poemTitle}>{poem.title || 'Sans titre'}</Text>
        <View style={[styles.poemMoodBadge, { borderColor: mood.color }]}>
          <View style={[styles.poemMoodDot, { backgroundColor: mood.color }]} />
          <Text style={styles.poemMoodLabel}>{mood.label}</Text>
        </View>
      </View>
      <Text style={styles.poemAuthor}>par {author}</Text>
      <View style={styles.poemMetaRow}>
        <Text style={styles.poemMeta}>{dateLabel}</Text>
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
