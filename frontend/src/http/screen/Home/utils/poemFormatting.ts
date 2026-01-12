/**
 * Formats a publication date for poem previews.
 */
export function formatPoemDate(value?: string | null) {
  if (!value) {
    return 'Date inconnue';
  }

  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    return 'Date inconnue';
  }

  return date.toLocaleDateString('fr-FR', { month: 'short', day: 'numeric' });
}

/**
 * Normalizes a status label for display.
 */
export function formatStatus(value?: string | null) {
  if (!value) {
    return 'En attente';
  }

  const cleaned = value.replace(/[_-]/g, ' ');
  return cleaned.charAt(0).toUpperCase() + cleaned.slice(1);
}
