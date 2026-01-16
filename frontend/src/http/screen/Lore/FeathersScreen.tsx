import { useLocalSearchParams } from 'expo-router';
import { LoreScreen } from './LoreScreen';
import { featherItems } from './loreData';

export default function FeathersScreen() {
  const params = useLocalSearchParams();
  const anchorKey = typeof params.feather === 'string' ? params.feather : undefined;

  return (
    <LoreScreen
      title="Plumes"
      subtitle="Des marqueurs de resonance qui accompagnent la lecture."
      info={[
        { title: "Qu'est-ce que c'est", text: 'Une plume signale une resonance, jamais une recompense.' },
        { title: 'Que disent-elles', text: 'Bronze, Argent, Or indiquent la profondeur de circulation.' },
        { title: 'Quand', text: "Elles se revelent apres interaction ou lecture attentive." },
      ]}
      items={featherItems}
      initialAnchorKey={anchorKey}
    />
  );
}
