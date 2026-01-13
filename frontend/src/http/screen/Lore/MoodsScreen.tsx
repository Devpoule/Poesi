import { LoreScreen } from './LoreScreen';
import { moodItems } from './loreData';

export default function MoodsScreen() {
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
    />
  );
}
