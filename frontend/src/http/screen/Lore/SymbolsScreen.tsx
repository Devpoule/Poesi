import { useLocalSearchParams } from 'expo-router';
import { LoreScreen } from './LoreScreen';
import { symbolItems } from './loreData';

export default function SymbolsScreen() {
  const params = useLocalSearchParams();
  const anchorKey = typeof params.symbol === 'string' ? params.symbol : undefined;
  return (
    <LoreScreen
      title="Symboles"
      subtitle="Des figures discretes qui accompagnent l'envol."
      info={[
        { title: "Qu'est-ce que c'est", text: "Des signes visuels qui n'expliquent pas, ils orientent." },
        { title: 'Pourquoi', text: "Installer une lecture sensible et silencieuse." },
        { title: 'Quand', text: 'Lecture, revelation de mood, plumes, ou moments rares.' },
      ]}
      items={symbolItems}
      initialAnchorKey={anchorKey}
    />
  );
}
