import { useLocalSearchParams } from 'expo-router';
import { LoreScreen } from './LoreScreen';
import { relicItems } from './loreData';

export default function RelicsScreen() {
  const params = useLocalSearchParams();
  const anchorKey = typeof params.relic === 'string' ? params.relic : undefined;
  return (
    <LoreScreen
      title="Reliques"
      subtitle="Des traces rares liees a des moments symboliques."
      info={[
        { title: "Qu'est-ce que c'est", text: 'Une relique marque un moment, pas une performance.' },
        { title: 'Pourquoi', text: 'Celebrer la renaissance, la constance ou la purete.' },
        { title: 'Quand', text: 'Elles apparaissent, elles ne se chassent pas.' },
      ]}
      items={relicItems}
      initialAnchorKey={anchorKey}
    />
  );
}
