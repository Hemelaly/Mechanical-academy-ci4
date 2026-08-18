import React from 'react';
import { CheckIcon } from 'lucide-react';
import { cn } from '../../utils/cn';

export interface Step {
  id: string;
  label: string;
  hint?: string;
}

/** Compact segmented stepper — rounded-md segments, no pills, no glow. */
export function Stepper({
  steps,
  current,
  onStepChange,
  className





}: {steps: Step[];current: number;onStepChange?: (index: number) => void;className?: string;}) {
  return (
    <ol className={cn('grid gap-1 sm:grid-flow-col sm:auto-cols-fr', className)}>
      {steps.map((step, index) => {
        const done = index < current;
        const active = index === current;
        const clickable = Boolean(onStepChange) && index <= current;
        return (
          <li key={step.id}>
            <button
              type="button"
              disabled={!clickable}
              onClick={() => onStepChange?.(index)}
              aria-current={active ? 'step' : undefined}
              className={cn(
                'flex w-full items-center gap-2 rounded-md border px-2.5 py-2 text-left',
                'transition-[background-color,border-color,color] duration-150 ease-out',
                active && 'border-accent bg-accent-soft',
                done && 'border-line bg-surface hover:bg-surface-2',
                !active && !done && 'border-line bg-surface-2',
                !clickable && 'cursor-default'
              )}>
              
              <span
                className={cn(
                  'flex h-5 w-5 shrink-0 items-center justify-center rounded-md text-2xs font-semibold tnum',
                  active && 'bg-accent text-accent-fg',
                  done && 'bg-success-soft text-success',
                  !active && !done && 'border border-line-strong text-fg-subtle'
                )}>
                
                {done ? <CheckIcon className="h-3 w-3" /> : index + 1}
              </span>
              <span className="min-w-0">
                <span
                  className={cn(
                    'block truncate text-xs font-medium',
                    active ? 'text-accent' : done ? 'text-fg' : 'text-fg-muted'
                  )}>
                  
                  {step.label}
                </span>
                {step.hint ? <span className="block truncate text-2xs text-fg-subtle">{step.hint}</span> : null}
              </span>
            </button>
          </li>);

      })}
    </ol>);

}