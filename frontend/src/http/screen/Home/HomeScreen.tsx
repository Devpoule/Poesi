import { Animated } from 'react-native';
import { useRouter } from 'expo-router';
import { useAuth } from '../../../bootstrap/AuthProvider';
import { Screen } from '../../components/Screen';
import { HomeHero } from './components/HomeHero';
import { HomeHighlights } from './components/HomeHighlights';
import { HomeMoodSection } from './components/HomeMoodSection';
import { HomePortal } from './components/HomePortal';
import { HomeRecentPoems } from './components/HomeRecentPoems';
import { HomeRitual } from './components/HomeRitual';
import { HomeSymbolSection } from './components/HomeSymbolSection';
import { useHomeRevealAnimation } from './hooks/useHomeRevealAnimation';
import { useRecentPoems } from './hooks/useRecentPoems';
import { styles } from './styles';

/**
 * Home screen combining categories and recent poems.
 */
export default function HomeScreen() {
  const router = useRouter();
  const { tokens } = useAuth();
  const { items, isLoading, error, reload } = useRecentPoems();
  const { reveals, revealStyle } = useHomeRevealAnimation(7);

  const handleExplore = () => {
    router.push('/(tabs)/poems');
  };

  const handleWrite = () => {
    router.push(tokens ? '/(tabs)/write' : '/(auth)/login');
  };

  const writeLabel = tokens ? 'Écrire un texte' : 'Se connecter';

  return (
    <Screen scroll contentStyle={styles.page}>
      <Animated.View style={revealStyle(reveals[0])}>
        <HomeHero onExplore={handleExplore} onWrite={handleWrite} writeLabel={writeLabel} />
      </Animated.View>

      <Animated.View style={revealStyle(reveals[1])}>
        <HomeHighlights />
      </Animated.View>

      <Animated.View style={revealStyle(reveals[2])}>
        <HomeRitual />
      </Animated.View>

      <Animated.View style={revealStyle(reveals[3])}>
        <HomeMoodSection />
      </Animated.View>

      <Animated.View style={revealStyle(reveals[4])}>
        <HomeSymbolSection />
      </Animated.View>

      <Animated.View style={revealStyle(reveals[5])}>
        <HomeRecentPoems
          items={items}
          isLoading={isLoading}
          error={error}
          onReload={reload}
          onWrite={handleWrite}
          onViewAll={handleExplore}
        />
      </Animated.View>

      <Animated.View style={revealStyle(reveals[6])}>
        <HomePortal writeLabel={writeLabel} onWrite={handleWrite} />
      </Animated.View>
    </Screen>
  );
}
