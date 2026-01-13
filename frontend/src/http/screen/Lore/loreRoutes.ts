export type LoreRoute = {
  key: string;
  title: string;
  description: string;
  route: string;
  tag?: string;
};

export const loreRoutes: LoreRoute[] = [
  {
    key: 'totems',
    title: 'Totems',
    description: "Postures d'ecriture pour situer l'auteur.",
    route: '/(tabs)/guide/totems',
  },
  {
    key: 'feathers',
    title: 'Plumes',
    description: 'Marqueurs de resonance, jamais de score.',
    route: '/(tabs)/guide/feathers',
  },
  {
    key: 'moods',
    title: 'Moods',
    description: 'Tonalites choisies ou revelees.',
    route: '/(tabs)/guide/moods',
  },
  {
    key: 'relics',
    title: 'Reliques',
    description: 'Moments rares, symboliques, non competitifs.',
    route: '/(tabs)/guide/relics',
    tag: 'Rare',
  },
  {
    key: 'symbols',
    title: 'Symboles',
    description: "Figures de l'envol qui accompagnent la lecture.",
    route: '/(tabs)/guide/symbols',
  },
  {
    key: 'glossary',
    title: 'Glossaire',
    description: "Definitions et leviers d'interaction.",
    route: '/(tabs)/guide/glossary',
  },
];
