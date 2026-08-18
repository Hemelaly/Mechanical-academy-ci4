import React from 'react';
import { cn } from '../../utils/cn';

/**
 * Section — the default grouping primitive. Flat, hairline border, no elevation.
 * Use this instead of a Card unless the block is a KPI or wraps an interaction.
 */
export function Section({
  title,
  description,
  action,
  children,
  className,
  bodyClassName,
  as: Tag = 'section'








}: {title?: string;description?: string;action?: React.ReactNode;children: React.ReactNode;className?: string;bodyClassName?: string;as?: 'section' | 'div' | 'article';}) {
  return (
    <Tag className={cn('rounded-md border border-line bg-surface', className)}>
      {title ?
      <header className="flex items-center justify-between gap-3 border-b border-line px-4 py-2.5">
          <div className="min-w-0">
            <h2 className="truncate text-sm font-semibold text-fg">{title}</h2>
            {description ? <p className="truncate text-xs text-fg-muted">{description}</p> : null}
          </div>
          {action ? <div className="flex shrink-0 items-center gap-1.5">{action}</div> : null}
        </header> :
      null}
      <div className={cn(bodyClassName ?? 'p-4')}>{children}</div>
    </Tag>);

}

/** Card — reserved for KPIs and interaction groupings. */
export function Card({
  children,
  className,
  interactive




}: {children: React.ReactNode;className?: string;interactive?: boolean;}) {
  return (
    <div
      className={cn(
        'rounded-md border border-line bg-surface p-3.5 shadow-xs',
        interactive &&
        'transition-[border-color,box-shadow] duration-150 ease-out hover:border-line-strong hover:shadow-sm',
        className
      )}>
      
      {children}
    </div>);

}

export function SectionLabel({ children, className }: {children: React.ReactNode;className?: string;}) {
  return (
    <p className={cn('text-2xs font-semibold uppercase tracking-widest text-fg-subtle', className)}>{children}</p>);

}

export function Divider({ className }: {className?: string;}) {
  return <hr className={cn('border-0 border-t border-line', className)} />;
}