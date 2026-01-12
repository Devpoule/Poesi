export type MoodOption = {
  key: string;
  label: string;
  color: string;
};

export const moodOptions: MoodOption[] = [
  { key: 'neutre', label: 'Neutre', color: '#9B948B' },
  { key: 'rouge', label: 'Rouge', color: '#C24E44' },
  { key: 'orange', label: 'Orange', color: '#D4833D' },
  { key: 'jaune', label: 'Jaune', color: '#D9B12E' },
  { key: 'vert', label: 'Vert', color: '#3F7B5C' },
  { key: 'bleu', label: 'Bleu', color: '#3E6DA3' },
  { key: 'indigo', label: 'Indigo', color: '#4C4B9D' },
  { key: 'violet', label: 'Violet', color: '#7652A3' },
  { key: 'blanc', label: 'Blanc', color: '#DAD4CC' },
  { key: 'noir', label: 'Noir', color: '#2B2622' },
  { key: 'gris', label: 'Gris', color: '#7C756E' },
];

const moodAliases = new Map<string, string>([
  ['neutral', 'neutre'],
  ['neutre', 'neutre'],
  ['red', 'rouge'],
  ['rouge', 'rouge'],
  ['orange', 'orange'],
  ['yellow', 'jaune'],
  ['jaune', 'jaune'],
  ['green', 'vert'],
  ['vert', 'vert'],
  ['blue', 'bleu'],
  ['bleu', 'bleu'],
  ['indigo', 'indigo'],
  ['purple', 'violet'],
  ['violet', 'violet'],
  ['white', 'blanc'],
  ['blanc', 'blanc'],
  ['black', 'noir'],
  ['noir', 'noir'],
  ['grey', 'gris'],
  ['gray', 'gris'],
  ['gris', 'gris'],
]);

const moodByKey = new Map(moodOptions.map((mood) => [mood.key, mood]));

export function resolveMood(value?: string | null): MoodOption {
  if (!value) {
    return moodOptions[0];
  }

  const normalized = value.trim().toLowerCase();
  if (normalized.startsWith('#')) {
    return { key: 'custom', label: 'Mood', color: normalized };
  }

  const canonical = moodAliases.get(normalized);
  if (canonical) {
    return moodByKey.get(canonical) ?? moodOptions[0];
  }

  return {
    key: normalized,
    label: value,
    color: moodOptions[0].color,
  };
}

export function normalizeMoodKey(value?: string | null): string {
  if (!value) {
    return 'neutre';
  }

  const normalized = value.trim().toLowerCase();
  if (normalized.startsWith('#')) {
    return 'custom';
  }

  return moodAliases.get(normalized) ?? normalized;
}
