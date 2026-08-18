import React from 'react';
import { cn } from '../../utils/cn';

export interface TabItem {
  id: string;
  label: string;
  count?: number;
}

export function Tabs({
  items,
  value,
  onChange,
  className





}: {items: TabItem[];value: string;onChange: (id: string) => void;className?: string;}) {
  return (
    <div role="tablist" className={cn('flex items-center gap-4 border-b border-line', className)}>
      {items.map((item) => {
        const active = item.id === value;
        return (
          <button
            key={item.id}
            role="tab"
            type="button"
            aria-selected={active}
            onClick={() => onChange(item.id)}
            className={cn(
              '-mb-px flex items-center gap-1.5 border-b-2 px-0.5 pb-2 pt-1 text-sm font-medium',
              'transition-[color,border-color] duration-150 ease-out focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[color:var(--ring)] focus-visible:ring-offset-2 focus-visible:ring-offset-[color:var(--surface)]',
              active ? 'border-accent text-fg' : 'border-transparent text-fg-muted hover:text-fg'
            )}>
            
            {item.label}
            {typeof item.count === 'number' ?
            <span
              className={cn(
                'rounded-md px-1 py-px text-2xs tnum',
                active ? 'bg-accent-soft text-accent' : 'bg-surface-2 text-fg-subtle'
              )}>
              
                {item.count}
              </span> :
            null}
          </button>);

      })}
    </div>);

}

/** Compact segmented control — rounded-md segments, no pills. */
export function Segmented({
  items,
  value,
  onChange,
  className





}: {items: {id: string;label: string;}[];value: string;onChange: (id: string) => void;className?: string;}) {
  return (
    <div className={cn('inline-flex rounded-md border border-line bg-surface-2 p-0.5', className)}>
      {items.map((item) => {
        const active = item.id === value;
        return (
          <button
            key={item.id}
            type="button"
            aria-pressed={active}
            onClick={() => onChange(item.id)}
            className={cn(
              'rounded-md px-2.5 py-1 text-xs font-medium transition-[background-color,color,box-shadow] duration-150 ease-out',
              active ? 'bg-surface text-fg shadow-xs' : 'text-fg-muted hover:text-fg'
            )}>
            
            {item.label}
          </button>);

      })}
    </div>);

}