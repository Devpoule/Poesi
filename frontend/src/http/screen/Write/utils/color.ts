/**
 * Adds an alpha channel to a hex color when possible.
 */
export function withAlpha(color: string, alphaHex: string) {
  if (color.startsWith('#') && color.length === 7) {
    return `${color}${alphaHex}`;
  }
  return color;
}

/**
 * Converts a hex color to an rgba string with the given opacity.
 */
export function toRgba(color: string, opacity: number) {
  if (!color.startsWith('#') || color.length !== 7) {
    return color;
  }
  const clamped = Math.max(0, Math.min(1, opacity));
  const r = parseInt(color.slice(1, 3), 16);
  const g = parseInt(color.slice(3, 5), 16);
  const b = parseInt(color.slice(5, 7), 16);
  return `rgba(${r}, ${g}, ${b}, ${clamped})`;
}

/**
 * Mixes two hex colors using a weighted ratio.
 */
export function mixColor(base: string, mix: string, weight: number) {
  if (!base.startsWith('#') || base.length !== 7) {
    return base;
  }
  if (!mix.startsWith('#') || mix.length !== 7) {
    return base;
  }
  const clamped = Math.max(0, Math.min(1, weight));
  const r = parseInt(base.slice(1, 3), 16);
  const g = parseInt(base.slice(3, 5), 16);
  const b = parseInt(base.slice(5, 7), 16);
  const mr = parseInt(mix.slice(1, 3), 16);
  const mg = parseInt(mix.slice(3, 5), 16);
  const mb = parseInt(mix.slice(5, 7), 16);
  const rr = Math.round(r + (mr - r) * clamped);
  const gg = Math.round(g + (mg - g) * clamped);
  const bb = Math.round(b + (mb - b) * clamped);
  const hex = (value: number) => value.toString(16).padStart(2, '0');
  return `#${hex(rr)}${hex(gg)}${hex(bb)}`;
}

/**
 * Determines if a hex color is visually dark.
 */
export function isDarkColor(color: string) {
  if (!color.startsWith('#') || color.length !== 7) {
    return false;
  }
  const r = parseInt(color.slice(1, 3), 16);
  const g = parseInt(color.slice(3, 5), 16);
  const b = parseInt(color.slice(5, 7), 16);
  const luma = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
  return luma < 0.45;
}
