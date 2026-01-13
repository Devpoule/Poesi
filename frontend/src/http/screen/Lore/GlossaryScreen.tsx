import { Pressable, Text, View } from 'react-native';
import { useRouter } from 'expo-router';
import { Screen } from '../../components/Screen';
import { useStyles } from './styles';

const glossaryEntries = [
  {
    key: 'totem',
    title: 'Totem',
    text: "Posture d'ecriture choisie par l'auteur. Elle guide la maniere d'aborder un texte.",
  },
  {
    key: 'mood',
    title: 'Mood',
    text: "Tonalite d'un texte. Peut etre choisie par l'auteur ou deduite par les lecteurs.",
  },
  {
    key: 'plume',
    title: 'Plume',
    text: 'Marqueur de resonance. Bronze, Argent, Or signalent une circulation sensible.',
  },
  {
    key: 'symbole',
    title: 'Symbole',
    text: "Figure d'envol qui apparait lors de lectures ou de revelations.",
  },
  {
    key: 'relique',
    title: 'Relique',
    text: 'Trace rare offerte pour des moments symboliques, jamais a chasser.',
  },
  {
    key: 'resonance',
    title: 'Resonance',
    text: 'Echo collectif qui se construit par la lecture et la perception.',
  },
  {
    key: 'rituel',
    title: 'Rituel',
    text: 'Invitation douce a ecrire, une question qui ouvre la page.',
  },
  {
    key: 'neutre',
    title: 'Neutre',
    text: 'Mood par defaut: la tonalite reste a reveler par les lecteurs.',
  },
  {
    key: 'revelation',
    title: 'Revelation',
    text: 'Moment ou un mood ou une plume devient visible apres interaction.',
  },
  {
    key: 'lecture',
    title: 'Lecture active',
    text: 'Gestes simples qui indiquent une perception sans juger.',
  },
];

export default function GlossaryScreen() {
  const styles = useStyles();
  const router = useRouter();
  return (
    <Screen scroll contentStyle={styles.page}>
      <View style={styles.header}>
        <Text style={styles.title}>Glossaire</Text>
        <Text style={styles.subtitle}>
          Definitions des objets et leviers d'interaction dans Poesi.
        </Text>
        <Pressable style={styles.backLink} onPress={() => router.push('/(tabs)/guide')}>
          <Text style={styles.backLinkText}>Retour au guide</Text>
        </Pressable>
      </View>

      {glossaryEntries.map((entry) => (
        <View key={entry.key} style={styles.glossarySection}>
          <Text style={styles.glossaryTitle}>{entry.title}</Text>
          <Text style={styles.glossaryText}>{entry.text}</Text>
        </View>
      ))}
    </Screen>
  );
}
