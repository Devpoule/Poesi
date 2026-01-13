import { Text, View } from 'react-native';
import { HomeSectionHeader } from './HomeSectionHeader';
import { useStyles } from '../styles';

const journeySteps = [
  {
    key: 'explore',
    title: 'Explorer',
    text: 'Parcourir la galerie et laisser une premiere resonance.',
  },
  {
    key: 'perceive',
    title: 'Ressentir',
    text: 'Choisir un mood ou laisser la lecture reveler la tonalite.',
  },
  {
    key: 'write',
    title: 'Ecrire',
    text: "Ecrire en silence, puis partager lorsque le texte s'envole.",
  },
];

export function HomeJourney() {
  const styles = useStyles();
  return (
    <View style={styles.section}>
      <HomeSectionHeader
        title="Parcours"
        hint="Un chemin simple pour entrer dans l'experience."
      />
      <View style={styles.journeyRow}>
        {journeySteps.map((step, index) => (
          <View key={step.key} style={styles.journeyCard}>
            <Text style={styles.journeyIndex}>{`0${index + 1}`}</Text>
            <Text style={styles.journeyTitle}>{step.title}</Text>
            <Text style={styles.journeyText}>{step.text}</Text>
          </View>
        ))}
      </View>
    </View>
  );
}
