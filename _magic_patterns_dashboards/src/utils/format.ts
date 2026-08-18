/** Currency + number helpers for the Mozambican metical (MZN). */

export function formatMZN(value: number, opts?: {compact?: boolean;}): string {
  if (opts?.compact && Math.abs(value) >= 1000) {
    const millions = value / 1_000_000;
    if (Math.abs(value) >= 1_000_000) {
      return `${millions.toFixed(millions >= 10 ? 0 : 1).replace('.', ',')}M MZN`;
    }
    return `${(value / 1000).toFixed(0)} mil MZN`;
  }
  return `${formatNumber(value)} MZN`;
}

export function formatNumber(value: number, decimals = 0): string {
  return value.
  toFixed(decimals).
  replace('.', ',').
  replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
}

export function formatPercent(value: number): string {
  const sign = value > 0 ? '+' : '';
  return `${sign}${formatNumber(value, 1)}%`;
}