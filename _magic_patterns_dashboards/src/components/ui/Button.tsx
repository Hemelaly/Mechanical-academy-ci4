import React from 'react';
import { cn } from '../../utils/cn';

export type ButtonVariant = 'primary' | 'secondary' | 'ghost' | 'danger';
export type ButtonSize = 'xs' | 'sm' | 'md';

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: ButtonVariant;
  size?: ButtonSize;
  icon?: React.ComponentType<{className?: string;}>;
  iconRight?: React.ComponentType<{className?: string;}>;
  block?: boolean;
}

const base =
'inline-flex items-center justify-center gap-1.5 rounded-md font-medium whitespace-nowrap select-none ' +
'transition-[background-color,border-color,color,box-shadow] duration-150 ease-out ' +
'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[color:var(--ring)] focus-visible:ring-offset-2 focus-visible:ring-offset-[color:var(--canvas)] ' +
'disabled:pointer-events-none disabled:opacity-45';

const variants: Record<ButtonVariant, string> = {
  primary: 'bg-accent text-accent-fg shadow-xs hover:bg-accent-hover active:bg-accent-active',
  secondary: 'border border-line bg-surface text-fg hover:bg-surface-2 active:bg-surface-3',
  ghost: 'text-fg-muted hover:bg-surface-2 hover:text-fg active:bg-surface-3',
  danger: 'bg-danger text-white shadow-xs hover:brightness-110 active:brightness-95'
};

const sizes: Record<ButtonSize, string> = {
  xs: 'h-7 px-2 text-xs',
  sm: 'h-8 px-2.5 text-xs',
  md: 'h-9 px-3.5 text-sm'
};

const iconSizes: Record<ButtonSize, string> = {
  xs: 'h-3.5 w-3.5',
  sm: 'h-3.5 w-3.5',
  md: 'h-4 w-4'
};

export function Button({
  variant = 'secondary',
  size = 'md',
  icon: Icon,
  iconRight: IconRight,
  block,
  className,
  children,
  type = 'button',
  ...rest
}: ButtonProps) {
  return (
    <button
      type={type}
      className={cn(base, variants[variant], sizes[size], block && 'w-full', className)}
      {...rest}>
      
      {Icon ? <Icon className={iconSizes[size]} /> : null}
      {children}
      {IconRight ? <IconRight className={iconSizes[size]} /> : null}
    </button>);

}

interface IconButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  icon: React.ComponentType<{className?: string;}>;
  label: string;
  variant?: ButtonVariant;
}

export function IconButton({ icon: Icon, label, variant = 'ghost', className, ...rest }: IconButtonProps) {
  return (
    <button
      type="button"
      aria-label={label}
      title={label}
      className={cn(base, variants[variant], 'h-8 w-8 p-0', className)}
      {...rest}>
      
      <Icon className="h-4 w-4" />
    </button>);

}