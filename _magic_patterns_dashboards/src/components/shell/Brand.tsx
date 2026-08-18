import React from 'react';
import { brand } from '../../data/brand';
import { cn } from '../../utils/cn';

export function Brand({ collapsed, className }: {collapsed?: boolean;className?: string;}) {
  return (
    <div className={cn('flex items-center gap-2.5', className)}>
      <span
        aria-hidden="true"
        className="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-accent text-sm font-bold text-accent-fg">
        
        {brand.mark}
      </span>
      {!collapsed ?
      <span className="min-w-0">
          <span className="block truncate text-sm font-semibold tracking-tight text-fg">{brand.name}</span>
          <span className="block truncate text-2xs text-fg-subtle">{brand.tagline}</span>
        </span> :
      null}
    </div>);

}