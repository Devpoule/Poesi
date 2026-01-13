import { useMemo } from 'react';
import { ThemeColors, useTheme } from '../../../../support/theme/tokens';

export type SymbolVariant = 'wings' | 'halo' | 'horizon' | 'meteor' | 'whirl';

export type SymbolItem = {
  key: string;
  label: string;
  variant: SymbolVariant;
  color: string;
};

export function getSymbolItems(colors: ThemeColors): SymbolItem[] {
  return [
    { key: 'ailes', label: 'Ailes', variant: 'wings', color: colors.accentStrong },
    { key: 'halo', label: 'Halo', variant: 'halo', color: colors.accentSoft },
    { key: 'horizon', label: 'Horizon', variant: 'horizon', color: colors.border },
    { key: 'meteore', label: 'Meteore', variant: 'meteor', color: colors.danger },
    { key: 'tourbillon', label: 'Tourbillon', variant: 'whirl', color: colors.textSecondary },
  ];
}

export function useSymbolItems(): SymbolItem[] {
  const { theme } = useTheme();
  return useMemo(() => getSymbolItems(theme.colors), [theme.colors]);
}
