import { colors } from '../../../../support/theme/tokens';

export type SymbolVariant = 'wings' | 'halo' | 'horizon' | 'meteor' | 'whirl';

export type SymbolItem = {
  key: string;
  label: string;
  variant: SymbolVariant;
  color: string;
};

export const symbolItems: SymbolItem[] = [
  { key: 'ailes', label: 'Ailes', variant: 'wings', color: colors.accentStrong },
  { key: 'halo', label: 'Halo', variant: 'halo', color: '#8A6B48' },
  { key: 'horizon', label: 'Horizon', variant: 'horizon', color: '#3E6DA3' },
  { key: 'meteore', label: 'Météore', variant: 'meteor', color: colors.danger },
  { key: 'tourbillon', label: 'Tourbillon', variant: 'whirl', color: '#4C4B9D' },
];
