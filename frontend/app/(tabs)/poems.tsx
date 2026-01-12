import FeedScreen from '../../src/http/screen/Feed/FeedScreen';

export default function PoemsRoute() {
  return (
    <FeedScreen
      title="Poèmes"
      subtitle="Galerie publique en attente de résonance."
      ctaLabel="Nouveau texte"
    />
  );
}
