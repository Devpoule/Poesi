import { Animated } from 'react-native';
import { useRouter } from 'expo-router';
import { useAuth } from '../../../bootstrap/AuthProvider';
import { Screen } from '../../components/Screen';
import { HomeHero } from './components/HomeHero';
import { HomeHighlights } from './components/HomeHighlights';
import { HomeJourney } from './components/HomeJourney';
import { HomeLoreSection } from './components/HomeLoreSection';
import { MoodPaletteSection } from '../../components/MoodPaletteSection';
import { HomeRitual } from './components/HomeRitual';
import { useHomeRevealAnimation } from './hooks/useHomeRevealAnimation';
import { useStyles } from './styles';

/**
 * Home screen combining entry points and lore guidance.
 */
export default function HomeScreen() {
  const styles = useStyles();
  const router = useRouter();
  const { tokens } = useAuth();
  const { reveals, revealStyle } = useHomeRevealAnimation(6);

  const handleExplore = () => {
    router.push('/(tabs)/poems');
  };

  const handleGuide = () => {
    router.push('/(tabs)/guide');
  };

  const handleLore = (route: string) => {
    router.push(route);
  };

  const handleWrite = () => {
    router.push(tokens ? '/(tabs)/write' : '/(auth)/login');
  };

  const writeLabel = tokens ? 'Ecrire un texte' : 'Se connecter';

  return (
    <Screen scroll contentStyle={styles.page}>
      <Animated.View style={revealStyle(reveals[0])}>
        <HomeHero
          onExplore={handleExplore}
          onWrite={handleWrite}
          onGuide={handleGuide}
          writeLabel={writeLabel}
        />
      </Animated.View>

      <Animated.View style={revealStyle(reveals[1])}>
        <HomeHighlights />
      </Animated.View>

      <Animated.View style={revealStyle(reveals[2])}>
        <MoodPaletteSection />
      </Animated.View>

      <Animated.View style={revealStyle(reveals[3])}>
        <HomeJourney />
      </Animated.View>

      <Animated.View style={revealStyle(reveals[4])}>
        <HomeLoreSection onNavigate={(item) => handleLore(item.route)} />
      </Animated.View>

      <Animated.View style={revealStyle(reveals[5])}>
        <HomeRitual />
      </Animated.View>
    </Screen>
  );
}
