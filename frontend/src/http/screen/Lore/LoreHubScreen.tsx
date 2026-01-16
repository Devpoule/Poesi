import { useRouter } from 'expo-router';
import { useTheme } from '../../../support/theme/tokens';
import { PageLayout } from '../../components/PageLayout';
import { CardGrid } from '../../components/CardGrid';
import { useStyles } from './styles';
import { loreRoutes } from './loreRoutes';

export default function LoreHubScreen() {
  const styles = useStyles();
  const router = useRouter();
  const { theme } = useTheme();

  const routeImages: Record<string, any> = {
    totems: require('../../../../assets/totems/cards/totem_card_egg.png'),
    feathers: require('../../../../assets/feathers/cards/feather_card_gold.png'),
    relics: require('../../../../assets/relics/cards/relic_card_phoenix_feather.png'),
    symbols: require('../../../../assets/symbols/cards/symbol_card_halo.png'),
  };

  const gridItems = loreRoutes.map((item) => ({
    key: item.key,
    title: item.title,
    description: item.description,
    tag: item.tag,
    accent: theme.colors.accentSoft,
    image: routeImages[item.key],
  }));

  return (
    <PageLayout
      title="Guide Poesi"
      subtitle="Comprendre les reperes symboliques pour mieux circuler dans Poesi."
      contentStyle={styles.page}
    >
      <CardGrid
        items={gridItems}
        onItemPress={(key) => {
          const target = loreRoutes.find((i) => i.key === key);
          if (target) router.push(target.route);
        }}
        hideAccentMarker
        compact
      />
    </PageLayout>
  );
}
