import totems from '../../../../../backend/resources/lore/totems.initial.json';
import feathers from '../../../../../backend/resources/lore/feathers.initial.json';
import moods from '../../../../../backend/resources/lore/moods.initial.json';
import relics from '../../../../../backend/resources/lore/relics.initial.json';
import symbols from '../../../../../backend/resources/lore/symbols.initial.json';
import { moodOptions, normalizeMoodKey } from '../../../support/theme/moods';

type LoreItem = {
  key: string;
  title: string;
  description: string;
  tag?: string;
  accent?: string;
  image?: any;
  anchor?: string;
};

type RawLoreItem = {
  key: string;
  label?: string;
  name?: string;
  description: string;
  rarity?: string;
};

const moodColorByKey = new Map(moodOptions.map((mood) => [mood.key, mood.color]));

const rarityLabels: Record<string, string> = {
  epic: 'Rare',
  legendary: 'Mythique',
};

function mapLoreItems(rawItems: RawLoreItem[]): LoreItem[] {
  return rawItems.map((entry) => ({
    key: entry.key,
    title: entry.label ?? entry.name ?? entry.key,
    description: entry.description,
    tag: entry.rarity ? rarityLabels[entry.rarity] ?? entry.rarity : undefined,
    anchor: entry.key,
  }));
}

const totemCardImages: Record<string, any> = {
  egg: require('../../../../assets/totems/cards/totem_card_egg.png'),
  crow: require('../../../../assets/totems/cards/totem_card_crow.png'),
  falcon: require('../../../../assets/totems/cards/totem_card_falcon.png'),
  owl: require('../../../../assets/totems/cards/totem_card_owl.png'),
  parrot: require('../../../../assets/totems/cards/totem_card_parrot.png'),
  sparrow: require('../../../../assets/totems/cards/totem_card_sparrow.png'),
  swan: require('../../../../assets/totems/cards/totem_card_swan.png'),
};
const featherCardImages: Record<string, any> = {
  bronze: require('../../../../assets/feathers/cards/feather_card_bronze.png'),
  silver: require('../../../../assets/feathers/cards/feather_card_silver.png'),
  gold: require('../../../../assets/feathers/cards/feather_card_gold.png'),
};
const relicCardImages: Record<string, any> = {
  phoenix_feather: require('../../../../assets/relics/cards/relic_card_phoenix_feather.png'),
  dragon_scale: require('../../../../assets/relics/cards/relic_card_dragon_scale.png'),
  pegasis_wings: require('../../../../assets/relics/cards/relic_card_pegasus_wings.png'),
  pegasis: require('../../../../assets/relics/cards/relic_card_pegasus_wings.png'), // alias safeguard
  pegasus_wings: require('../../../../assets/relics/cards/relic_card_pegasus_wings.png'),
  unicorn_horn: require('../../../../assets/relics/cards/relic_card_unicorn_horn.png'),
  lycan_claw: require('../../../../assets/relics/cards/relic_card_lycan_claw.png'),
};
const symbolCardImages: Record<string, any> = {
  wings: require('../../../../assets/symbols/cards/symbol_card_wings.png'),
  meteor_shard: require('../../../../assets/symbols/cards/symbol_card_meteor_shard.png'),
  vortex: require('../../../../assets/symbols/cards/symbol_card_tourbillon.png'),
  tourbillon: require('../../../../assets/symbols/cards/symbol_card_tourbillon.png'),
  horizon: require('../../../../assets/symbols/cards/symbol_card_horizon.png'),
  halo: require('../../../../assets/symbols/cards/symbol_card_halo.png'),
};

export const totemItems = (totems as RawLoreItem[]).map((entry) => ({
  ...mapLoreItems([entry])[0],
  image: totemCardImages[entry.key],
  anchor: entry.key,
}));
export const featherItems = (feathers as RawLoreItem[]).map((entry) => ({
  ...mapLoreItems([entry])[0],
  image: featherCardImages[entry.key],
}));
export const relicItems = (relics as RawLoreItem[]).map((entry) => ({
  ...mapLoreItems([entry])[0],
  image: relicCardImages[entry.key],
}));
export const symbolItems = (symbols as RawLoreItem[]).map((entry) => ({
  ...mapLoreItems([entry])[0],
  image: symbolCardImages[entry.key],
}));

export const moodItems = (moods as RawLoreItem[]).map((entry) => {
  const canonical = normalizeMoodKey(entry.key);
  return {
    key: entry.key,
    title: entry.label ?? entry.key,
    description: entry.description,
    accent: moodColorByKey.get(canonical),
    anchor: entry.key,
  };
});

export type { LoreItem };
