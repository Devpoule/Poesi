import React, { useMemo } from 'react';
import { Platform, Pressable, StyleSheet, Text, View, useWindowDimensions } from 'react-native';
import { CardPortrait } from './CardPortrait';
import { ThemeColors, spacing, typography, useTheme } from '../../support/theme/tokens';

/**
 * Standard responsive card item used by CardGrid.
 */
export type CardGridItem = {
  key: string;
  title: string;
  description: string;
  tag?: string;
  accent?: string;
  image?: any;
};

type CardGridProps = {
  items: CardGridItem[];
  selectedKey?: string;
  onItemPress?: (key: string) => void;
  hideAccentMarker?: boolean;
  compact?: boolean;
};

/**
 * Responsive 2–4 columns grid for cards (image or color fallback),
 * with optional selection and click handling.
 */
export function CardGrid({
  items,
  selectedKey,
  onItemPress,
  hideAccentMarker = false,
  compact = false,
}: CardGridProps) {
  const { theme } = useTheme();
  const { width } = useWindowDimensions();
  const styles = useMemo(() => createStyles(theme.colors, width, compact), [theme.colors, width, compact]);

  return (
    <View style={styles.list}>
      {items.map((item) => (
        <Pressable
          key={item.key}
          style={[
            styles.itemCard,
            selectedKey === item.key ? styles.itemCardSelected : null,
          ]}
          disabled={!onItemPress}
          onPress={() => onItemPress?.(item.key)}
          android_ripple={{ color: 'transparent' }}
        >
          <View style={styles.itemHeader}>
            {item.accent && !hideAccentMarker ? (
              <View style={[styles.itemAccent, { backgroundColor: item.accent }]} />
            ) : null}
            <Text style={styles.itemTitle}>{item.title}</Text>
            {item.tag ? (
              <View style={styles.itemTag}>
                <Text style={styles.itemTagText}>{item.tag}</Text>
              </View>
            ) : null}
          </View>

          {item.image ? (
            <CardPortrait source={item.image} style={styles.itemImage} />
          ) : (
            <View
              style={[
                styles.itemImage,
                styles.itemColorCard,
                item.accent ? { backgroundColor: item.accent } : null,
              ]}
            />
          )}

          <Text style={styles.itemText}>{item.description}</Text>
        </Pressable>
      ))}
    </View>
  );
}

function createStyles(colors: ThemeColors, width: number, compact: boolean) {
  const colCount = width >= 1200 ? 4 : width >= 900 ? 3 : 2; // 2 min, 4 max
  const itemWidth = colCount === 4 ? '23%' : colCount === 3 ? '30%' : '48%';
  const itemMaxWidth = colCount === 4 ? 180 : colCount === 3 ? 220 : 260;
  const itemMinWidth = colCount === 4 ? 150 : colCount === 3 ? 180 : 140;

  return StyleSheet.create({
    list: {
      marginBottom: spacing.lg,
      flexDirection: 'row',
      flexWrap: 'wrap',
      gap: spacing.sm,
      justifyContent: 'flex-start',
    },
    itemCard: {
      backgroundColor: colors.surface,
      borderRadius: 20,
      padding: spacing.sm,
      borderWidth: 1,
      borderColor: colors.border,
      marginBottom: spacing.sm,
      width: itemWidth,
      maxWidth: compact ? Math.min(itemMaxWidth, 210) : itemMaxWidth,
      minWidth: compact ? Math.min(itemMinWidth, 150) : itemMinWidth,
      alignSelf: 'flex-start',
      ...Platform.select({
        web: { boxShadow: '0px 8px 20px rgba(0,0,0,0.06)' } as any,
        default: {
          shadowColor: '#000',
          shadowOpacity: 0.05,
          shadowRadius: 8,
          shadowOffset: { width: 0, height: 4 },
          elevation: 2,
        },
      }),
    },
    itemCardSelected: {
      borderColor: colors.accent,
      backgroundColor: colors.surfaceMuted,
    },
    itemHeader: {
      flexDirection: 'row',
      alignItems: 'center',
      marginBottom: spacing.xs,
      gap: spacing.xs,
    },
    itemAccent: {
      width: 8,
      height: 8,
      borderRadius: 4,
      marginRight: spacing.xs,
    },
    itemTitle: {
      fontSize: typography.body,
      fontFamily: typography.headingFont,
      color: colors.textPrimary,
      flex: 1,
    },
    itemTag: {
      paddingVertical: 2,
      paddingHorizontal: spacing.sm,
      borderRadius: 999,
      backgroundColor: colors.surfaceMuted,
    },
    itemTagText: {
      fontSize: typography.small,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
    },
    itemImage: {
      width: '60%',
      maxWidth: compact ? 180 : 220,
      alignSelf: 'center',
      aspectRatio: 0.72,
      marginBottom: spacing.xs,
      ...Platform.select({
        web: {
          width: compact ? '48%' : '52%',
          maxWidth: compact ? 160 : 180,
        },
        default: {},
      }),
    },
    itemColorCard: {
      borderRadius: 14,
      borderWidth: 1,
      borderColor: colors.border,
      backgroundColor: colors.surfaceMuted,
    },
    itemText: {
      fontSize: typography.caption,
      fontFamily: typography.fontFamily,
      color: colors.textSecondary,
    },
  });
}
