import { LoreScreen } from './LoreScreen';
import { symbolItems } from './loreData';

export default function SymbolsScreen() {
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
    />
  );
}
