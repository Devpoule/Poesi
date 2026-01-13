import { Pressable, Text, View } from 'react-native';
import { useRouter } from 'expo-router';
import { Screen } from '../../components/Screen';
import { LoreItem } from './loreData';
import { useStyles } from './styles';

type InfoCard = {
  title: string;
  text: string;
};

type LoreScreenProps = {
  title: string;
  subtitle: string;
  info: InfoCard[];
  items: LoreItem[];
};

export function LoreScreen({ title, subtitle, info, items }: LoreScreenProps) {
  const styles = useStyles();
  const router = useRouter();
  return (
    <Screen scroll contentStyle={styles.page}>
      <View style={styles.header}>
        <Text style={styles.title}>{title}</Text>
        <Text style={styles.subtitle}>{subtitle}</Text>
        <Pressable style={styles.backLink} onPress={() => router.push('/(tabs)/guide')}>
          <Text style={styles.backLinkText}>Retour au guide</Text>
        </Pressable>
      </View>

      <View style={styles.infoRow}>
        {info.map((card) => (
          <View key={card.title} style={styles.infoCard}>
            <Text style={styles.infoTitle}>{card.title}</Text>
            <Text style={styles.infoText}>{card.text}</Text>
          </View>
        ))}
      </View>

      <View style={styles.list}>
        {items.map((item) => (
          <View key={item.key} style={styles.itemCard}>
            <View style={styles.itemHeader}>
              {item.accent ? (
                <View style={[styles.itemAccent, { backgroundColor: item.accent }]} />
              ) : null}
              <Text style={styles.itemTitle}>{item.title}</Text>
              {item.tag ? (
                <View style={styles.itemTag}>
                  <Text style={styles.itemTagText}>{item.tag}</Text>
                </View>
              ) : null}
            </View>
            <Text style={styles.itemText}>{item.description}</Text>
          </View>
        ))}
      </View>
    </Screen>
  );
}
