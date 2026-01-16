import { View } from 'react-native';
import { useRouter } from 'expo-router';
import { useTheme } from '../../../../support/theme/tokens';
import { CardGrid } from '../../../components/CardGrid';
import { Section } from '../../../components/Section';
import { HomeSectionHeader } from './HomeSectionHeader';
import { useStyles } from '../styles';

const discoverItems = [
  {
    key: 'write',
    title: "L'atelier d'ecriture",
    description: "Ouvre l'atelier et laisse ton texte prendre forme.",
    route: '/(tabs)/write',
  },
  {
    key: 'moods',
    title: 'Les moods',
    description: 'Choisis une ambiance qui teinte ton parcours et tes textes.',
    route: '/(tabs)/guide/moods',
  },
  {
    key: 'feathers',
    title: 'Les plumes',
    description: 'Comprends les marqueurs de resonance, jamais de score.',
    route: '/(tabs)/guide/feathers',
  },
  {
    key: 'totems',
    title: 'Les totems',
    description: 'Situe ta posture d\'ecriture du moment.',
    route: '/(tabs)/guide/totems',
  },
  {
    key: 'relics',
    title: 'Les reliques',
    description: 'Moments rares, symboliques, non competitifs.',
    route: '/(tabs)/guide/relics',
  },
  {
    key: 'symbols',
    title: 'Les symboles',
    description: "Figures d'envol qui accompagnent la lecture.",
    route: '/(tabs)/guide/symbols',
  },
  {
    key: 'glossary',
    title: 'Le glossaire',
    description: "Definitions et leviers d'interaction, en un coup d'oeil.",
    route: '/(tabs)/guide/glossary',
  },
];

/**
 * Feature discovery section to guide users toward key areas.
 */
export function HomeDiscover() {
  const styles = useStyles();
  const router = useRouter();
  const { theme } = useTheme();

  const itemImages: Record<string, any> = {
    totems: require('../../../../../assets/totems/cards/totem_card_egg.png'),
    feathers: require('../../../../../assets/feathers/cards/feather_card_gold.png'),
    relics: require('../../../../../assets/relics/cards/relic_card_phoenix_feather.png'),
    symbols: require('../../../../../assets/symbols/cards/symbol_card_halo.png'),
  };

  const gridItems = discoverItems.map((item) => ({
    key: item.key,
    title: item.title,
    description: item.description,
    accent: theme.colors.accentSoft,
    image: itemImages[item.key],
  }));

  return (
    <View style={styles.section}>
      <HomeSectionHeader
        title="Decouvrir Poesi"
        hint="Les portes pour explorer les fonctionnalites de l'application."
      />
      <Section>
        <CardGrid
          items={gridItems}
          onItemPress={(key) => {
            const target = discoverItems.find((i) => i.key === key);
            if (target) router.push(target.route);
          }}
          hideAccentMarker
          compact
        />
      </Section>
    </View>
  );
}
