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
  }));
}

export const totemItems = mapLoreItems(totems as RawLoreItem[]);
export const featherItems = mapLoreItems(feathers as RawLoreItem[]);
export const relicItems = mapLoreItems(relics as RawLoreItem[]);
export const symbolItems = mapLoreItems(symbols as RawLoreItem[]);

export const moodItems = (moods as RawLoreItem[]).map((entry) => {
  const canonical = normalizeMoodKey(entry.key);
  return {
    key: entry.key,
    title: entry.label ?? entry.key,
    description: entry.description,
    accent: moodColorByKey.get(canonical),
  };
});

export type { LoreItem };
