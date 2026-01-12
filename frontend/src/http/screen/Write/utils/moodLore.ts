import moodsLore from '../../../../../../api/resources/lore/moods.initial.json';

export type MoodLoreEntry = {
  key: string;
  label: string;
  description?: string;
  icon?: string;
};

const moodLoreByKey = new Map<string, MoodLoreEntry>(
  (moodsLore as MoodLoreEntry[]).map((entry) => [entry.key, entry])
);

const moodApiKeyByKey: Record<string, string> = {
  neutre: 'neutral',
  rouge: 'red',
  orange: 'orange',
  jaune: 'yellow',
  vert: 'green',
  bleu: 'blue',
  indigo: 'indigo',
  violet: 'violet',
  blanc: 'white',
  noir: 'black',
  gris: 'grey',
};

/**
 * Returns the lore entry for a mood key when available.
 */
export function getMoodLore(key: string) {
  return moodLoreByKey.get(moodApiKeyByKey[key] ?? key);
}
