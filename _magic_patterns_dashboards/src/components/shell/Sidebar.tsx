import React from 'react';
import { NavLink } from 'react-router-dom';
import { ChevronsLeftIcon, ChevronsRightIcon, LogOutIcon, PanelsTopLeftIcon } from 'lucide-react';
import { cn } from '../../utils/cn';
import type { RoleConfig } from '../../data/nav';
import { Avatar } from '../ui/Bits';
import { IconButton } from '../ui/Button';
import { Brand } from './Brand';

function NavRow({
  item,
  collapsed,
  end




}: {item: {label: string;to: string;icon: React.ComponentType<{className?: string;}>;badge?: string;};collapsed: boolean;end?: boolean;}) {
  const Icon = item.icon;
  return (
    <NavLink
      to={item.to}
      end={end}
      title={collapsed ? item.label : undefined}
      className={({ isActive }) =>
      cn(
        'group relative flex items-center gap-2.5 rounded-md px-2 py-1.5 text-sm font-medium',
        'transition-[background-color,color] duration-150 ease-out',
        'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[color:var(--ring)] focus-visible:ring-offset-2 focus-visible:ring-offset-[color:var(--surface)]',
        collapsed && 'justify-center px-0',
        isActive ?
        'bg-accent-soft text-accent' :
        'text-fg-muted hover:bg-surface-2 hover:text-fg active:bg-surface-3'
      )
      }>
      
      {({ isActive }) =>
      <>
          {isActive ?
        <span aria-hidden="true" className="absolute left-0 top-1.5 h-[calc(100%-12px)] w-0.5 rounded-md bg-accent" /> :
        null}
          <Icon className="h-4 w-4 shrink-0" />
          {!collapsed ? <span className="min-w-0 flex-1 truncate">{item.label}</span> : null}
          {!collapsed && item.badge ?
        <span className="rounded-md bg-surface-3 px-1 py-px text-2xs font-semibold text-fg-muted tnum">
              {item.badge}
            </span> :
        null}
        </>
      }
    </NavLink>);

}

export function SidebarContent({
  config,
  collapsed,
  onToggleCollapse




}: {config: RoleConfig;collapsed: boolean;onToggleCollapse?: () => void;}) {
  return (
    <div className="flex h-full flex-col bg-surface">
      <div
        className={cn(
          'flex h-14 shrink-0 items-center border-b border-line px-3',
          collapsed ? 'justify-center' : 'justify-between'
        )}>
        
        <Brand collapsed={collapsed} />
        {onToggleCollapse ?
        <IconButton
          icon={collapsed ? ChevronsRightIcon : ChevronsLeftIcon}
          label={collapsed ? 'Expandir menu' : 'Recolher menu'}
          onClick={onToggleCollapse}
          className={collapsed ? 'hidden' : ''} /> :

        null}
      </div>

      <nav aria-label="Menu principal" className="scroll-area flex-1 overflow-y-auto px-2 py-3">
        {!collapsed ?
        <p className="px-2 pb-1.5 text-2xs font-semibold uppercase tracking-widest text-fg-subtle">Menu</p> :
        null}
        <ul className="space-y-0.5">
          {config.nav.map((item, index) =>
          <li key={item.to}>
              <NavRow item={item} collapsed={collapsed} end={index === 0} />
            </li>
          )}
        </ul>

        <div className="my-3 border-t border-line" />
        {!collapsed ?
        <p className="px-2 pb-1.5 text-2xs font-semibold uppercase tracking-widest text-fg-subtle">Sistema</p> :
        null}
        <ul className="space-y-0.5">
          {config.secondary.map((item) =>
          <li key={item.to}>
              <NavRow item={item} collapsed={collapsed} />
            </li>
          )}
          <li>
            <NavRow item={{ label: 'Design system', to: '/design-system', icon: PanelsTopLeftIcon }} collapsed={collapsed} />
          </li>
        </ul>
      </nav>

      <div className="shrink-0 border-t border-line p-2">
        {collapsed ?
        <div className="flex flex-col items-center gap-1.5">
            <Avatar name={config.user.name} size="sm" />
            {onToggleCollapse ?
          <IconButton icon={ChevronsRightIcon} label="Expandir menu" onClick={onToggleCollapse} /> :
          null}
          </div> :

        <div className="flex items-center gap-2.5 rounded-md px-1.5 py-1.5">
            <Avatar name={config.user.name} size="md" />
            <span className="min-w-0 flex-1">
              <span className="block truncate text-xs font-medium text-fg">{config.user.name}</span>
              <span className="block truncate text-2xs text-fg-subtle">{config.label}</span>
            </span>
            <IconButton icon={LogOutIcon} label="Terminar sessão" />
          </div>
        }
      </div>
    </div>);

}