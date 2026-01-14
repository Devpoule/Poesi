import { Text, View } from 'react-native';
import { useRouter } from 'expo-router';
import { Button } from '../../../components/Button';
import { HomeSectionHeader } from './HomeSectionHeader';
import { useStyles } from '../styles';

const discoverItems = [
  {
    key: 'write',
    title: "Découvrir l'écriture",
    description: 'Ouvre l’atelier et laisse ton texte prendre forme.',
    route: '/(tabs)/write',
    cta: "Aller à l'atelier",
  },
  {
    key: 'moods',
    title: 'Découvrir les moods',
    description: "Choisis une ambiance qui teinte ton parcours et tes textes.",
    route: '/(tabs)/guide/moods',
    cta: 'Voir les moods',
  },
  {
    key: 'feathers',
    title: 'Découvrir les plumes',
    description: 'Comprends les marqueurs de résonance, jamais de score.',
    route: '/(tabs)/guide/feathers',
    cta: 'Voir les plumes',
  },
  {
    key: 'totems',
    title: 'Découvrir les totems',
    description: "Situe ta posture d'écriture du moment.",
    route: '/(tabs)/guide/totems',
    cta: 'Voir les totems',
  },
  {
    key: 'relics',
    title: 'Découvrir les relics',
    description: 'Moments rares, symboliques, non compétitifs.',
    route: '/(tabs)/guide/relics',
    cta: 'Voir les relics',
  },
  {
    key: 'symbols',
    title: 'Découvrir les symbols',
    description: "Figures d'envol qui accompagnent la lecture.",
    route: '/(tabs)/guide/symbols',
    cta: 'Voir les symbols',
  },
  {
    key: 'glossary',
    title: 'Glossaire',
    description: 'Définitions et leviers d’interaction, en un coup d’œil.',
    route: '/(tabs)/guide/glossary',
    cta: 'Ouvrir le glossaire',
  },
];

/**
 * Feature discovery section to guide users toward key areas.
 */
export function HomeDiscover() {
  const styles = useStyles();
  const router = useRouter();

  return (
    <View style={styles.section}>
      <HomeSectionHeader
        title="Découvrir Poesi"
        hint="Les portes pour explorer les fonctionnalités de l’application."
      />
      <View style={styles.discoverGrid}>
        {discoverItems.map((item) => (
          <View key={item.key} style={styles.discoverCard}>
            <Text style={styles.discoverTitle}>{item.title}</Text>
            <Text style={styles.discoverText}>{item.description}</Text>
            <Button
              title={item.cta}
              onPress={() => router.push(item.route)}
              variant="primary"
              style={styles.discoverButton}
            />
          </View>
        ))}
      </View>
    </View>
  );
}
