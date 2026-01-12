export type HighlightItem = {
  key: string;
  title: string;
  description: string;
};

export const highlightItems: HighlightItem[] = [
  {
    key: 'resonance',
    title: 'Résonance',
    description: "Ici, un texte vit par ses échos, jamais par un score.",
  },
  {
    key: 'lenteur',
    title: 'Lenteur',
    description: "Une cadence calme pour écrire sans pression.",
  },
  {
    key: 'rituels',
    title: 'Rituels',
    description: 'Des gestes simples pour entendre le monde autrement.',
  },
];

export const heroBadges = ['Sans score', 'Lecture lente', 'Symboles'];
