import { useLocalSearchParams } from 'expo-router';
import { LoreScreen } from './LoreScreen';
import { totemItems } from './loreData';

export default function TotemsScreen() {
  const params = useLocalSearchParams();
  const anchorKey =
    typeof params.totem === 'string'
      ? params.totem
      : typeof params.anchor === 'string'
        ? params.anchor
        : undefined;

  return (
    <LoreScreen
      title="Totems"
      subtitle="Une posture d'ecriture choisie, visible sans hierarchie."
      info={[
        { title: "Qu'est-ce que c'est", text: "Un totem est une posture d'ecriture, pas une identite fixe." },
        { title: 'Que represente-t-il', text: "Une maniere d'aborder le texte: retenue, rythme, observation." },
        { title: 'Quand', text: "Choisi par l'auteur pour accompagner ses textes." },
      ]}
      items={totemItems}
      initialAnchorKey={anchorKey}
    />
  );
}
