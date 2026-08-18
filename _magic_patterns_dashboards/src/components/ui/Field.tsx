import React from 'react';
import { ChevronDownIcon } from 'lucide-react';
import { cn } from '../../utils/cn';

const control =
'w-full rounded-md border border-line bg-surface text-sm text-fg placeholder:text-fg-subtle ' +
'transition-[border-color,box-shadow] duration-150 ease-out ' +
'focus:outline-none focus:border-accent focus:ring-2 focus:ring-[color:var(--ring-soft)] ' +
'disabled:cursor-not-allowed disabled:bg-surface-2 disabled:text-fg-subtle';

export function Label({
  children,
  htmlFor,
  hint,
  required





}: {children: React.ReactNode;htmlFor?: string;hint?: string;required?: boolean;}) {
  return (
    <div className="mb-1.5 flex items-baseline justify-between gap-2">
      <label htmlFor={htmlFor} className="text-xs font-medium text-fg">
        {children}
        {required ? <span className="ml-0.5 text-danger">*</span> : null}
      </label>
      {hint ? <span className="text-2xs text-fg-subtle">{hint}</span> : null}
    </div>);

}

interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
  icon?: React.ComponentType<{className?: string;}>;
  invalid?: boolean;
}

export function Input({ icon: Icon, invalid, className, ...rest }: InputProps) {
  return (
    <div className="relative">
      {Icon ?
      <Icon className="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-fg-subtle" /> :
      null}
      <input
        className={cn(
          control,
          'h-9 px-2.5',
          Icon && 'pl-8',
          invalid && 'border-danger focus:border-danger focus:ring-[color:var(--ring-danger)]',
          className
        )}
        {...rest} />
      
    </div>);

}

interface TextareaProps extends React.TextareaHTMLAttributes<HTMLTextAreaElement> {}

export function Textarea({ className, rows = 4, ...rest }: TextareaProps) {
  return <textarea rows={rows} className={cn(control, 'px-2.5 py-2', className)} {...rest} />;
}

interface SelectProps extends React.SelectHTMLAttributes<HTMLSelectElement> {
  options: {value: string;label: string;}[];
}

export function Select({ options, className, ...rest }: SelectProps) {
  return (
    <div className="relative">
      <select className={cn(control, 'h-9 appearance-none pl-2.5 pr-8', className)} {...rest}>
        {options.map((option) =>
        <option key={option.value} value={option.value}>
            {option.label}
          </option>
        )}
      </select>
      <ChevronDownIcon className="pointer-events-none absolute right-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-fg-subtle" />
    </div>);

}

export function Checkbox({
  label,
  description,
  ...rest
}: React.InputHTMLAttributes<HTMLInputElement> & {label: string;description?: string;}) {
  return (
    <label className="flex cursor-pointer items-start gap-2">
      <input
        type="checkbox"
        className="mt-0.5 h-4 w-4 rounded-sm border-line-strong text-accent focus:ring-2 focus:ring-[color:var(--ring-soft)]"
        {...rest} />
      
      <span className="text-sm text-fg">
        {label}
        {description ? <span className="block text-xs text-fg-muted">{description}</span> : null}
      </span>
    </label>);

}

export function FieldGroup({
  label,
  hint,
  required,
  error,
  htmlFor,
  children,
  className








}: {label: string;hint?: string;required?: boolean;error?: string;htmlFor?: string;children: React.ReactNode;className?: string;}) {
  return (
    <div className={className}>
      <Label htmlFor={htmlFor} hint={hint} required={required}>
        {label}
      </Label>
      {children}
      {error ? <p className="mt-1 text-xs text-danger">{error}</p> : null}
    </div>);

}