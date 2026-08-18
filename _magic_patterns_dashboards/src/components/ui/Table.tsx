import React from 'react';
import { cn } from '../../utils/cn';

export function Table({ children, className }: {children: React.ReactNode;className?: string;}) {
  return (
    <div className="w-full overflow-x-auto">
      <table className={cn('w-full border-collapse text-sm', className)}>{children}</table>
    </div>);

}

export function THead({ children }: {children: React.ReactNode;}) {
  return <thead className="bg-surface-2">{children}</thead>;
}

export function TBody({ children }: {children: React.ReactNode;}) {
  return <tbody className="divide-y divide-line">{children}</tbody>;
}

export function TR({
  children,
  className,
  hoverable = true




}: {children: React.ReactNode;className?: string;hoverable?: boolean;}) {
  return (
    <tr
      className={cn(
        hoverable && 'transition-colors duration-100 ease-out hover:bg-surface-2',
        className
      )}>
      
      {children}
    </tr>);

}

export function TH({
  children,
  className,
  align = 'left',
  scope = 'col'





}: {children?: React.ReactNode;className?: string;align?: 'left' | 'right' | 'center';scope?: 'col' | 'row';}) {
  return (
    <th
      scope={scope}
      className={cn(
        'border-b border-line px-3 py-2 text-2xs font-semibold uppercase tracking-wider text-fg-muted',
        align === 'right' && 'text-right',
        align === 'center' && 'text-center',
        align === 'left' && 'text-left',
        className
      )}>
      
      {children}
    </th>);

}

export function TD({
  children,
  className,
  align = 'left',
  colSpan





}: {children?: React.ReactNode;className?: string;align?: 'left' | 'right' | 'center';colSpan?: number;}) {
  return (
    <td
      colSpan={colSpan}
      className={cn(
        'px-3 py-2 align-middle text-sm text-fg',
        align === 'right' && 'text-right',
        align === 'center' && 'text-center',
        className
      )}>
      
      {children}
    </td>);

}