import { HomeMoodSection } from '../screen/Home/components/HomeMoodSection';

type MoodPaletteSectionProps = {
  selectedKey?: string;
  onSelect?: (key: string) => void;
  title?: string;
  hint?: string;
  showDescription?: boolean;
  columns?: number;
};

/**
 * Reusable mood palette block (used on home and guide).
 */
export function MoodPaletteSection({
  selectedKey,
  onSelect,
  title = 'Ambiances',
  hint = "Choisis la couleur qui reflete ton etat d'esprit.",
  showDescription = false,
  columns,
}: MoodPaletteSectionProps) {
  const resolvedColumns = columns ?? 1;

  return (
    <HomeMoodSection
      selectedKey={selectedKey}
      onSelect={onSelect}
      title={title}
      hint={hint}
      showDescription={showDescription}
      columns={resolvedColumns}
    />
  );
}
