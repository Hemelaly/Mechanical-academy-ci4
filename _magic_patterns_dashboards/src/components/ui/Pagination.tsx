import React from 'react';
import { ChevronLeftIcon, ChevronRightIcon } from 'lucide-react';
import { cn } from '../../utils/cn';

export function Pagination({
  page,
  pageCount,
  total,
  pageSize,
  onPageChange,
  className







}: {page: number;pageCount: number;total: number;pageSize: number;onPageChange: (page: number) => void;className?: string;}) {
  const from = (page - 1) * pageSize + 1;
  const to = Math.min(page * pageSize, total);
  const pages = Array.from({ length: pageCount }, (_, index) => index + 1);

  const buttonBase =
  'inline-flex h-7 w-7 items-center justify-center rounded-md border text-xs font-medium transition-[background-color,border-color,color] duration-150 ease-out disabled:pointer-events-none disabled:opacity-40';

  return (
    <div className={cn('flex flex-wrap items-center justify-between gap-3', className)}>
      <p className="text-xs text-fg-muted tnum">
        A mostrar <span className="font-medium text-fg">{from}</span>–
        <span className="font-medium text-fg">{to}</span> de <span className="font-medium text-fg">{total}</span>
      </p>
      <nav aria-label="Paginação" className="flex items-center gap-1">
        <button
          type="button"
          className={cn(buttonBase, 'border-line bg-surface text-fg-muted hover:bg-surface-2 hover:text-fg')}
          onClick={() => onPageChange(Math.max(1, page - 1))}
          disabled={page === 1}
          aria-label="Página anterior">
          
          <ChevronLeftIcon className="h-3.5 w-3.5" />
        </button>
        {pages.map((p) =>
        <button
          key={p}
          type="button"
          aria-current={p === page ? 'page' : undefined}
          onClick={() => onPageChange(p)}
          className={cn(
            buttonBase,
            p === page ?
            'border-accent bg-accent text-accent-fg' :
            'border-line bg-surface text-fg-muted hover:bg-surface-2 hover:text-fg'
          )}>
          
            {p}
          </button>
        )}
        <button
          type="button"
          className={cn(buttonBase, 'border-line bg-surface text-fg-muted hover:bg-surface-2 hover:text-fg')}
          onClick={() => onPageChange(Math.min(pageCount, page + 1))}
          disabled={page === pageCount}
          aria-label="Página seguinte">
          
          <ChevronRightIcon className="h-3.5 w-3.5" />
        </button>
      </nav>
    </div>);

}