import React from 'react';
import { cn } from '../../utils/cn';

export type BadgeTone = 'neutral' | 'accent' | 'success' | 'warn' | 'danger' | 'info';

const tones: Record<BadgeTone, string> = {
  neutral: 'bg-surface-2 text-fg-muted border-line',
  accent: 'bg-accent-soft text-accent border-transparent',
  success: 'bg-success-soft text-success border-transparent',
  warn: 'bg-warn-soft text-warn border-transparent',
  danger: 'bg-danger-soft text-danger border-transparent',
  info: 'bg-info-soft text-info border-transparent'
};

const dots: Record<BadgeTone, string> = {
  neutral: 'bg-fg-subtle',
  accent: 'bg-accent',
  success: 'bg-success',
  warn: 'bg-warn',
  danger: 'bg-danger',
  info: 'bg-info'
};

export function Badge({
  tone = 'neutral',
  dot,
  children,
  className





}: {tone?: BadgeTone;dot?: boolean;children: React.ReactNode;className?: string;}) {
  return (
    <span
      className={cn(
        'inline-flex items-center gap-1.5 rounded-md border px-1.5 py-0.5 text-xs font-medium',
        tones[tone],
        className
      )}>
      
      {dot ? <span className={cn('h-1.5 w-1.5 rounded-full', dots[tone])} /> : null}
      {children}
    </span>);

}