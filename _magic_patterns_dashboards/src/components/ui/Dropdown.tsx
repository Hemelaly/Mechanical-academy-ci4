import React, { useEffect, useRef, useState } from 'react';
import { AnimatePresence, motion } from 'framer-motion';
import { cn } from '../../utils/cn';

export interface DropdownItem {
  id: string;
  label: string;
  icon?: React.ComponentType<{className?: string;}>;
  tone?: 'default' | 'danger';
  onSelect?: () => void;
}

export function Dropdown({
  trigger,
  items,
  align = 'right',
  header,
  width = 'w-48'






}: {trigger: (props: {open: boolean;toggle: () => void;}) => React.ReactNode;items: DropdownItem[];align?: 'left' | 'right';header?: React.ReactNode;width?: string;}) {
  const [open, setOpen] = useState(false);
  const ref = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (!open) return;
    function onDocClick(event: MouseEvent) {
      if (ref.current && !ref.current.contains(event.target as Node)) setOpen(false);
    }
    function onKey(event: KeyboardEvent) {
      if (event.key === 'Escape') setOpen(false);
    }
    document.addEventListener('mousedown', onDocClick);
    document.addEventListener('keydown', onKey);
    return () => {
      document.removeEventListener('mousedown', onDocClick);
      document.removeEventListener('keydown', onKey);
    };
  }, [open]);

  return (
    <div ref={ref} className="relative">
      {trigger({ open, toggle: () => setOpen((v) => !v) })}
      <AnimatePresence>
        {open ?
        <motion.div
          role="menu"
          initial={{ opacity: 0, y: -4, scale: 0.98 }}
          animate={{ opacity: 1, y: 0, scale: 1 }}
          exit={{ opacity: 0, y: -2, scale: 0.99 }}
          transition={{ duration: 0.15, ease: [0.23, 1, 0.32, 1] }}
          className={cn(
            'absolute z-40 mt-1.5 overflow-hidden rounded-md border border-line bg-surface p-1 shadow-pop',
            width,
            align === 'right' ? 'right-0' : 'left-0'
          )}>
          
            {header ? <div className="border-b border-line px-2 pb-2 pt-1.5">{header}</div> : null}
            <div className="pt-1">
              {items.map((item) =>
            <button
              key={item.id}
              type="button"
              role="menuitem"
              onClick={() => {
                item.onSelect?.();
                setOpen(false);
              }}
              className={cn(
                'flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-left text-xs font-medium',
                'transition-colors duration-100 ease-out',
                item.tone === 'danger' ?
                'text-danger hover:bg-danger-soft' :
                'text-fg-muted hover:bg-surface-2 hover:text-fg'
              )}>
              
                  {item.icon ? <item.icon className="h-3.5 w-3.5" /> : null}
                  {item.label}
                </button>
            )}
            </div>
          </motion.div> :
        null}
      </AnimatePresence>
    </div>);

}