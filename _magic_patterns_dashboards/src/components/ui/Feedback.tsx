import React from 'react';
import { AnimatePresence, motion } from 'framer-motion';
import { CheckCircle2Icon, InfoIcon, TriangleAlertIcon, XIcon } from 'lucide-react';
import { cn } from '../../utils/cn';
import { Button, IconButton } from './Button';

export function EmptyState({
  icon: Icon,
  title,
  description,
  action,
  className






}: {icon: React.ComponentType<{className?: string;}>;title: string;description?: string;action?: React.ReactNode;className?: string;}) {
  return (
    <div className={cn('flex flex-col items-center justify-center px-6 py-12 text-center', className)}>
      <div className="mb-3 flex h-10 w-10 items-center justify-center rounded-md border border-line bg-surface-2">
        <Icon className="h-4 w-4 text-fg-subtle" />
      </div>
      <h3 className="text-sm font-semibold text-fg">{title}</h3>
      {description ? <p className="mt-1 max-w-sm text-xs leading-5 text-fg-muted">{description}</p> : null}
      {action ? <div className="mt-4 flex items-center gap-2">{action}</div> : null}
    </div>);

}

export function Skeleton({ className }: {className?: string;}) {
  return (
    <div
      className={cn(
        'animate-shimmer rounded-md bg-[length:200%_100%] bg-gradient-to-r from-surface-2 via-surface-3 to-surface-2',
        className
      )} />);


}

export function Modal({
  open,
  onClose,
  title,
  description,
  children,
  footer







}: {open: boolean;onClose: () => void;title: string;description?: string;children: React.ReactNode;footer?: React.ReactNode;}) {
  return (
    <AnimatePresence>
      {open ?
      <div className="fixed inset-0 z-50 flex items-start justify-center p-4 sm:items-center">
          <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          transition={{ duration: 0.16, ease: [0.23, 1, 0.32, 1] }}
          className="absolute inset-0 bg-[#07090d]/50"
          onClick={onClose} />
        
          <motion.div
          role="dialog"
          aria-modal="true"
          aria-label={title}
          initial={{ opacity: 0, scale: 0.97, y: 6 }}
          animate={{ opacity: 1, scale: 1, y: 0 }}
          exit={{ opacity: 0, scale: 0.98, y: 4 }}
          transition={{ duration: 0.22, ease: [0.23, 1, 0.32, 1] }}
          className="relative w-full max-w-md rounded-md border border-line bg-surface shadow-pop">
          
            <header className="flex items-start justify-between gap-3 border-b border-line px-4 py-3">
              <div>
                <h2 className="text-sm font-semibold text-fg">{title}</h2>
                {description ? <p className="mt-0.5 text-xs text-fg-muted">{description}</p> : null}
              </div>
              <IconButton icon={XIcon} label="Fechar" onClick={onClose} />
            </header>
            <div className="px-4 py-3.5 text-sm text-fg-muted">{children}</div>
            {footer ?
          <footer className="flex items-center justify-end gap-2 border-t border-line bg-surface-2 px-4 py-2.5">
                {footer}
              </footer> :
          null}
          </motion.div>
        </div> :
      null}
    </AnimatePresence>);

}

export type ToastTone = 'success' | 'danger' | 'info';

const toastIcons: Record<ToastTone, React.ComponentType<{className?: string;}>> = {
  success: CheckCircle2Icon,
  danger: TriangleAlertIcon,
  info: InfoIcon
};

const toastAccents: Record<ToastTone, string> = {
  success: 'text-success',
  danger: 'text-danger',
  info: 'text-info'
};

export function Toast({
  tone = 'info',
  title,
  description,
  onClose,
  action,
  className







}: {tone?: ToastTone;title: string;description?: string;onClose?: () => void;action?: string;className?: string;}) {
  const Icon = toastIcons[tone];
  return (
    <div
      role="status"
      className={cn(
        'flex w-full max-w-sm items-start gap-2.5 rounded-md border border-line bg-surface px-3 py-2.5 shadow-pop',
        className
      )}>
      
      <Icon className={cn('mt-0.5 h-4 w-4 shrink-0', toastAccents[tone])} />
      <div className="min-w-0 flex-1">
        <p className="text-sm font-medium text-fg">{title}</p>
        {description ? <p className="mt-0.5 text-xs text-fg-muted">{description}</p> : null}
        {action ?
        <Button variant="ghost" size="xs" className="-ml-2 mt-1.5 text-accent hover:text-accent">
            {action}
          </Button> :
        null}
      </div>
      {onClose ? <IconButton icon={XIcon} label="Fechar" onClick={onClose} className="h-6 w-6" /> : null}
    </div>);

}