import { useEffect, useMemo, useState } from 'react';
import { MoodPaletteSection } from '../../components/MoodPaletteSection';
import { normalizeMoodKey } from '../../../support/theme/moods';
import { useTheme } from '../../../support/theme/tokens';
import { LoreScreen } from './LoreScreen';
import { moodItems } from './loreData';

export default function MoodsScreen() {
  const { accentKey, setAccentKey } = useTheme();
  const [selectedKey, setSelectedKey] = useState<string>(accentKey ?? 'neutre');
  const selectedItem = useMemo(() => {
    const target = normalizeMoodKey(selectedKey);
    return moodItems.find((item) => normalizeMoodKey(item.key) === target) ?? moodItems[0];
  }, [selectedKey]);

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

  return (
    <LoreScreen
      title="Moods"
      subtitle="La tonalite d'un texte, choisie ou percue."
      info={[
        { title: "Qu'est-ce que c'est", text: 'Une couleur de lecture, fixe ou revelee.' },
        { title: 'Pourquoi', text: 'Guider la perception sans jugement ni score.' },
        { title: 'Quand', text: "Choisi par l'auteur, ou deduit par les lecteurs si neutre." },
      ]}
      items={[selectedItem]}
      extraContent={
        <MoodPaletteSection
          selectedKey={selectedKey}
          onSelect={handleSelect}
          title="Ambiances"
          hint="Choisis la couleur qui reflete ton etat d'esprit."
        />
      }
    />
  );
}
