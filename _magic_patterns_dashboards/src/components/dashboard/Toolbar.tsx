import React from 'react';
import { SearchIcon } from 'lucide-react';
import { Input, Select } from '../ui/Field';

export interface ToolbarFilter {
  id: string;
  ariaLabel: string;
  width?: string;
  options: {value: string;label: string;}[];
  value?: string;
  onChange?: (value: string) => void;
}

/** Shared toolbar for list pages: search + selects + right-aligned actions. */
export function Toolbar({
  placeholder,
  query,
  onQueryChange,
  filters = [],
  actions






}: {placeholder: string;query: string;onQueryChange: (value: string) => void;filters?: ToolbarFilter[];actions?: React.ReactNode;}) {
  return (
    <div className="flex flex-wrap items-center gap-2 border-b border-line px-4 py-2.5">
      <div className="w-full sm:w-60">
        <Input
          icon={SearchIcon}
          placeholder={placeholder}
          aria-label={placeholder}
          value={query}
          onChange={(event) => onQueryChange(event.target.value)}
          className="h-8 text-xs" />
        
      </div>
      {filters.map((filter) =>
      <div key={filter.id} className={filter.width ?? 'w-36'}>
          <Select
          aria-label={filter.ariaLabel}
          className="h-8 text-xs"
          options={filter.options}
          value={filter.value}
          onChange={(event) => filter.onChange?.(event.target.value)} />
        
        </div>
      )}
      {actions ? <div className="ml-auto flex items-center gap-2">{actions}</div> : null}
    </div>);

}