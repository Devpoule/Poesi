import React from 'react';
import { Image, ImageSourcePropType, StyleProp, StyleSheet, View, ViewStyle } from 'react-native';

type CardPortraitProps = {
  source: ImageSourcePropType;
  style?: StyleProp<ViewStyle>;
};

// A reusable portrait frame (MTG-like ratio) to keep card visuals consistent.
export function CardPortrait({ source, style }: CardPortraitProps) {
  return (
    <View style={[styles.container, style]}>
      <Image source={source} style={styles.image} resizeMode="cover" />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    width: '100%',
    aspectRatio: 0.72, // portrait ratio similar to trading cards
    borderRadius: 16,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: 'rgba(0,0,0,0.08)',
  },
  image: {
    width: '100%',
    height: '100%',
  },
});

