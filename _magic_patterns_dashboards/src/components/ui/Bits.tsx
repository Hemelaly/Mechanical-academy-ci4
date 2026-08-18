import React from 'react';
import { ArrowDownRightIcon, ArrowUpRightIcon, UploadCloudIcon } from 'lucide-react';
import { cn } from '../../utils/cn';
import { Skeleton } from './Feedback';

export function Avatar({
  name,
  src,
  size = 'md',
  className





}: {name: string;src?: string;size?: 'xs' | 'sm' | 'md' | 'lg';className?: string;}) {
  const sizes = {
    xs: 'h-5 w-5 text-2xs',
    sm: 'h-6 w-6 text-2xs',
    md: 'h-8 w-8 text-xs',
    lg: 'h-10 w-10 text-sm'
  };
  const initials = name.
  split(' ').
  filter(Boolean).
  slice(0, 2).
  map((part) => part[0]).
  join('').
  toUpperCase();

  return (
    <span
      className={cn(
        'inline-flex shrink-0 items-center justify-center overflow-hidden rounded-md border border-line bg-surface-2 font-semibold text-fg-muted',
        sizes[size],
        className
      )}>
      
      {src ? <img src={src} alt="" className="h-full w-full object-cover" /> : initials}
    </span>);

}

export function ProgressBar({
  value,
  label,
  tone = 'accent',
  className





}: {value: number;label?: string;tone?: 'accent' | 'success';className?: string;}) {
  return (
    <div className={className}>
      {label ?
      <div className="mb-1 flex items-center justify-between text-2xs text-fg-muted">
          <span>{label}</span>
          <span className="tnum font-medium text-fg">{value}%</span>
        </div> :
      null}
      <div
        role="progressbar"
        aria-valuenow={value}
        aria-valuemin={0}
        aria-valuemax={100}
        className="h-1.5 w-full overflow-hidden rounded-md bg-surface-3">
        
        <div
          className={cn('h-full rounded-md', tone === 'accent' ? 'bg-accent' : 'bg-success')}
          style={{ width: `${Math.min(100, Math.max(0, value))}%` }} />
        
      </div>
    </div>);

}

export function StatCard({
  label,
  value,
  delta,
  deltaLabel,
  icon: Icon,
  className







}: {label: string;value: string;delta?: number;deltaLabel?: string;icon?: React.ComponentType<{className?: string;}>;className?: string;}) {
  const positive = (delta ?? 0) >= 0;
  return (
    <div className={cn('rounded-md border border-line bg-surface p-3.5 shadow-xs', className)}>
      <div className="flex items-start justify-between gap-2">
        <p className="text-2xs font-semibold uppercase tracking-widest text-fg-subtle">{label}</p>
        {Icon ? <Icon className="h-4 w-4 shrink-0 text-fg-subtle" /> : null}
      </div>
      <p className="mt-2 text-2xl font-semibold leading-none tracking-tight text-fg tnum">{value}</p>
      {typeof delta === 'number' && delta !== 0 ?
      <div className="mt-2 flex items-center gap-1.5">
          <span
          className={cn(
            'inline-flex items-center gap-0.5 rounded-md px-1 py-px text-2xs font-semibold tnum',
            positive ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger'
          )}>
          
            {positive ?
          <ArrowUpRightIcon className="h-3 w-3" /> :

          <ArrowDownRightIcon className="h-3 w-3" />
          }
            {Math.abs(delta).toFixed(1).replace('.', ',')}%
          </span>
          {deltaLabel ? <span className="truncate text-2xs text-fg-subtle">{deltaLabel}</span> : null}
        </div> :
      deltaLabel ?
      <p className="mt-2 truncate text-2xs text-fg-subtle">{deltaLabel}</p> :
      null}
    </div>);

}

export function StatCardSkeleton() {
  return (
    <div className="rounded-md border border-line bg-surface p-3.5 shadow-xs">
      <Skeleton className="h-2.5 w-20" />
      <Skeleton className="mt-3 h-6 w-24" />
      <Skeleton className="mt-3 h-3 w-16" />
    </div>);

}

export function UploadZone({
  title = 'Arraste ficheiros ou clique para carregar',
  hint = 'MP4, PDF ou ZIP até 500 MB',
  className




}: {title?: string;hint?: string;className?: string;}) {
  return (
    <button
      type="button"
      className={cn(
        'flex w-full flex-col items-center justify-center gap-1.5 rounded-md border border-dashed border-line-strong bg-surface-2 px-4 py-7 text-center',
        'transition-[background-color,border-color] duration-150 ease-out hover:border-accent hover:bg-accent-soft',
        'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[color:var(--ring)] focus-visible:ring-offset-2 focus-visible:ring-offset-[color:var(--surface)]',
        className
      )}>
      
      <UploadCloudIcon className="h-5 w-5 text-fg-subtle" />
      <span className="text-xs font-medium text-fg">{title}</span>
      <span className="text-2xs text-fg-subtle">{hint}</span>
    </button>);

}