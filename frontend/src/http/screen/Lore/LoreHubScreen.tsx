import { Pressable, Text, View } from 'react-native';
import { useRouter } from 'expo-router';
import { Screen } from '../../components/Screen';
import { useStyles } from './styles';
import { loreRoutes } from './loreRoutes';

export default function LoreHubScreen() {
  const styles = useStyles();
  const router = useRouter();

  return (
    <Screen scroll contentStyle={styles.page}>
      <View style={styles.header}>
        <Text style={styles.title}>Guide Poesi</Text>
        <Text style={styles.subtitle}>
          Comprendre les reperes symboliques pour mieux circuler dans Poesi.
        </Text>
      </View>

      <View style={styles.grid}>
        {loreRoutes.map((item) => (
          <Pressable
            key={item.key}
            style={styles.gridCard}
            onPress={() => router.push(item.route)}
          >
            {item.tag ? (
              <View style={styles.gridCardTag}>
                <Text style={styles.gridCardTagText}>{item.tag}</Text>
              </View>
            ) : null}
            <Text style={styles.gridCardTitle}>{item.title}</Text>
            <Text style={styles.gridCardText}>{item.description}</Text>
          </Pressable>
        ))}
      </View>
    </Screen>
  );
}
