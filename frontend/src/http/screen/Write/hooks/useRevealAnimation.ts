import { useEffect, useRef } from 'react';
import { Animated, Platform } from 'react-native';

const useNativeDriver = Platform.OS !== 'web';

/**
 * Provides staggered reveal values and a helper for animated styles.
 */
export function useRevealAnimation(count: number) {
  const reveals = useRef(
    Array.from({ length: count }, () => new Animated.Value(0))
  ).current;

  useEffect(() => {
    Animated.stagger(
      120,
      reveals.map((value) =>
        Animated.timing(value, {
          toValue: 1,
          duration: 320,
          useNativeDriver,
        })
      )
    ).start();
  }, [reveals]);

  const revealStyle = (value: Animated.Value) => ({
    opacity: value,
    transform: [
      {
        translateY: value.interpolate({
          inputRange: [0, 1],
          outputRange: [10, 0],
        }),
      },
    ],
  });

  return { reveals, revealStyle };
}
