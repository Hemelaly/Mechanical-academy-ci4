import React from 'react';
import {
  Area,
  AreaChart,
  Bar,
  BarChart,
  CartesianGrid,
  Line,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis } from
'recharts';
import { useTheme } from '../../contexts/ThemeContext';
import { enrollmentSeries, instructorEarningsSeries, revenueSeries } from '../../data/finance';
import { formatMZN, formatNumber } from '../../utils/format';

function usePalette() {
  const { theme } = useTheme();
  const dark = theme === 'dark';
  return {
    accent: dark ? '#2f6ff0' : '#2563eb',
    accentSoft: dark ? '#2f6ff0' : '#2563eb',
    neutral: dark ? '#4b5768' : '#c3cbd6',
    success: dark ? '#46c37b' : '#17803d',
    grid: dark ? 'rgba(255,255,255,0.07)' : '#e9edf2',
    axis: dark ? '#6b7788' : '#8b96a6',
    tooltipBg: dark ? '#11151c' : '#ffffff',
    tooltipBorder: dark ? 'rgba(255,255,255,0.12)' : '#e2e7ed',
    tooltipText: dark ? '#e7ebf1' : '#0f172a'
  };
}

function TooltipShell({
  active,
  payload,
  label,
  formatter





}: {active?: boolean;payload?: {name: string;value: number;color: string;}[];label?: string;formatter: (value: number) => string;}) {
  const palette = usePalette();
  if (!active || !payload?.length) return null;
  return (
    <div
      className="rounded-md border px-2.5 py-2 text-xs shadow-pop"
      style={{ background: palette.tooltipBg, borderColor: palette.tooltipBorder, color: palette.tooltipText }}>
      
      <p className="mb-1 text-2xs font-semibold uppercase tracking-wider opacity-60">{label}</p>
      {payload.map((entry) =>
      <p key={entry.name} className="flex items-center gap-1.5 tnum">
          <span className="h-1.5 w-1.5 rounded-full" style={{ background: entry.color }} />
          <span className="capitalize opacity-70">{entry.name}</span>
          <span className="font-semibold">{formatter(entry.value)}</span>
        </p>
      )}
    </div>);

}

const axisProps = (color: string) => ({
  stroke: color,
  tick: { fill: color, fontSize: 10 },
  tickLine: false,
  axisLine: false
});

export function RevenueChart({ height = 208 }: {height?: number;}) {
  const palette = usePalette();
  return (
    <ResponsiveContainer width="100%" height={height}>
      <AreaChart data={revenueSeries} margin={{ top: 4, right: 4, bottom: 0, left: -14 }}>
        <defs>
          <linearGradient id="revenueFill" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stopColor={palette.accent} stopOpacity={0.18} />
            <stop offset="100%" stopColor={palette.accent} stopOpacity={0.02} />
          </linearGradient>
        </defs>
        <CartesianGrid stroke={palette.grid} vertical={false} />
        <XAxis dataKey="month" {...axisProps(palette.axis)} />
        <YAxis
          {...axisProps(palette.axis)}
          width={52}
          tickFormatter={(value: number) => `${Math.round(value / 1000)}k`} />
        
        <Tooltip
          cursor={{ stroke: palette.grid }}
          content={<TooltipShell formatter={(value) => formatMZN(value)} />} />
        
        <Area
          type="monotone"
          dataKey="receita"
          name="Receita"
          stroke={palette.accent}
          strokeWidth={2}
          fill="url(#revenueFill)" />
        
        <Line
          type="monotone"
          dataKey="meta"
          name="Meta"
          stroke={palette.neutral}
          strokeWidth={1.5}
          strokeDasharray="4 4"
          dot={false} />
        
      </AreaChart>
    </ResponsiveContainer>);

}

export function EnrollmentsChart({ height = 208 }: {height?: number;}) {
  const palette = usePalette();
  return (
    <ResponsiveContainer width="100%" height={height}>
      <BarChart data={enrollmentSeries} margin={{ top: 4, right: 4, bottom: 0, left: -20 }} barGap={2}>
        <CartesianGrid stroke={palette.grid} vertical={false} />
        <XAxis dataKey="week" {...axisProps(palette.axis)} />
        <YAxis {...axisProps(palette.axis)} width={40} />
        <Tooltip
          cursor={{ fill: palette.grid }}
          content={<TooltipShell formatter={(value) => formatNumber(value)} />} />
        
        <Bar dataKey="novas" name="Novas inscrições" fill={palette.accent} radius={[3, 3, 0, 0]} maxBarSize={18} />
        <Bar dataKey="concluidas" name="Conclusões" fill={palette.neutral} radius={[3, 3, 0, 0]} maxBarSize={18} />
      </BarChart>
    </ResponsiveContainer>);

}

export function EarningsChart({ height = 208 }: {height?: number;}) {
  const palette = usePalette();
  return (
    <ResponsiveContainer width="100%" height={height}>
      <BarChart data={instructorEarningsSeries} margin={{ top: 4, right: 4, bottom: 0, left: -8 }}>
        <CartesianGrid stroke={palette.grid} vertical={false} />
        <XAxis dataKey="month" {...axisProps(palette.axis)} />
        <YAxis
          {...axisProps(palette.axis)}
          width={44}
          tickFormatter={(value: number) => `${Math.round(value / 1000)}k`} />
        
        <Tooltip
          cursor={{ fill: palette.grid }}
          content={<TooltipShell formatter={(value) => formatMZN(value)} />} />
        
        <Bar dataKey="ganhos" name="Ganhos" fill={palette.accent} radius={[3, 3, 0, 0]} maxBarSize={26} />
      </BarChart>
    </ResponsiveContainer>);

}