import { Text, View } from 'react-native';
import { HomeSectionHeader } from './HomeSectionHeader';
import { useStyles } from '../styles';
import { LoreRoute, loreRoutes } from '../../Lore/loreRoutes';
import { Button } from '../../../components/Button';

type HomeLoreSectionProps = {
  onNavigate: (route: LoreRoute) => void;
};

export function HomeLoreSection({ onNavigate }: HomeLoreSectionProps) {
  const styles = useStyles();
  return (
    <View style={styles.section}>
      <HomeSectionHeader
        title="Principes"
        hint="Une page par principe pour comprendre les items et leurs usages."
      />
      <View style={styles.loreGrid}>
        {loreRoutes.map((item) => (
          <View key={item.key} style={styles.loreCard}>
            {item.tag ? (
              <View style={styles.loreTag}>
                <Text style={styles.loreTagText}>{item.tag}</Text>
              </View>
            ) : null}
            <Text style={styles.loreTitle}>{item.title}</Text>
            <Text style={styles.loreText}>{item.description}</Text>
            <Button
              title="En savoir plus"
              onPress={() => onNavigate(item)}
              variant="primary"
              style={styles.loreButton}
            />
          </View>
        ))}
      </View>
    </View>
  );
}
