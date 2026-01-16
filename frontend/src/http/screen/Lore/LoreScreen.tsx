import { ReactNode, useEffect, useRef, useState } from 'react';
import { Pressable, ScrollView, Text, View } from 'react-native';
import { useRouter } from 'expo-router';
import { PageLayout } from '../../components/PageLayout';
import { Section } from '../../components/Section';
import { CardGrid } from '../../components/CardGrid';
import { LoreItem } from './loreData';
import { useStyles } from './styles';

type InfoCard = {
  title: string;
  text: string;
};

type LoreScreenProps = {
  title: string;
  subtitle: string;
  info: InfoCard[];
  items: LoreItem[];
  extraContent?: ReactNode;
  initialAnchorKey?: string;
  selectedKey?: string;
  onItemPress?: (key: string) => void;
  hideAccentMarker?: boolean;
  compactCards?: boolean;
};

export function LoreScreen({
  title,
  subtitle,
  info,
  items,
  extraContent,
  initialAnchorKey,
  selectedKey,
  onItemPress,
  hideAccentMarker = false,
  compactCards = false,
}: LoreScreenProps) {
  const styles = useStyles();
  const router = useRouter();
  const scrollRef = useRef<ScrollView>(null);
  const [contentReady, setContentReady] = useState(false);
  const anchorKey = initialAnchorKey;
  const positions = useRef<Record<string, number>>({});

  const handleItemLayout = (key: string) => (event: any) => {
    positions.current[key] = event.nativeEvent.layout.y;
  };

  useEffect(() => {
    if (!anchorKey || !contentReady) {
      return;
    }
    const y = positions.current[anchorKey];
    if (typeof y === 'number') {
      scrollRef.current?.scrollTo({ y: Math.max(y - 12, 0), animated: true });
    }
  }, [anchorKey, contentReady]);

  return (
    <PageLayout
      title={title}
      subtitle={subtitle}
      action={
        <Pressable style={styles.backLink} onPress={() => router.push('/(tabs)/guide')}>
          <Text style={styles.backLinkText}>Retour au guide</Text>
        </Pressable>
      }
      scrollRef={scrollRef}
      onContentSizeChange={() => setContentReady(true)}
      contentStyle={styles.page}
    >

      <Section>
        <View style={styles.infoRow}>
          {info.map((card) => (
            <View key={card.title} style={styles.infoCard}>
              <Text style={styles.infoTitle}>{card.title}</Text>
              <Text style={styles.infoText}>{card.text}</Text>
            </View>
          ))}
        </View>
      </Section>

      {extraContent}

      <View onLayout={handleItemLayout('grid')}>
        <CardGrid
          items={items}
          onItemPress={onItemPress}
          selectedKey={selectedKey}
          hideAccentMarker={hideAccentMarker}
          compact={compactCards}
        />
      </View>
    </PageLayout>
  );
}
