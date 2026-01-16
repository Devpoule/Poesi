import { useEffect, useState } from 'react';
import { useLocalSearchParams } from 'expo-router';
import { normalizeMoodKey } from '../../../support/theme/moods';
import { useTheme } from '../../../support/theme/tokens';
import { LoreScreen } from './LoreScreen';
import { moodItems } from './loreData';

export default function MoodsScreen() {
  const { accentKey, setAccentKey } = useTheme();
  const params = useLocalSearchParams();
  const [selectedKey, setSelectedKey] = useState<string>(accentKey ?? 'neutre');

  const handleSelect = (key: string) => {
    setSelectedKey(key);
    setAccentKey(key === 'neutre' ? null : key);
  };

  useEffect(() => {
    if (!accentKey) {
      setSelectedKey('neutre');
      return;
    }
    const canonical = normalizeMoodKey(accentKey);
    setSelectedKey(canonical);
    if (canonical !== accentKey) {
      setAccentKey(canonical);
    }
  }, [accentKey]);

  useEffect(() => {
    const paramMood =
      typeof params.mood === 'string'
        ? params.mood
        : typeof params.anchor === 'string'
          ? params.anchor
          : undefined;
    if (paramMood) {
      const normalized = normalizeMoodKey(paramMood);
      setSelectedKey(normalized);
      setAccentKey(normalized === 'neutre' ? null : normalized);
    }
  }, [params.mood, params.anchor]);

  return (
    <LoreScreen
      title="Moods"
      subtitle="La tonalite d'un texte, choisie ou percue."
      info={[
        { title: "Qu'est-ce que c'est", text: 'Une couleur de lecture, fixe ou revelee.' },
        { title: 'Pourquoi', text: 'Guider la perception sans jugement ni score.' },
        { title: 'Quand', text: "Choisi par l'auteur, ou deduit par les lecteurs si neutre." },
      ]}
      items={moodItems}
      initialAnchorKey={selectedKey}
      onItemPress={handleSelect}
      selectedKey={selectedKey}
      hideAccentMarker
      compactCards
    />
  );
}
