import React from 'react';
import { useNavigate } from 'react-router-dom';
import {
  BellIcon,
  ChevronDownIcon,
  ChevronRightIcon,
  CircleUserRoundIcon,
  LogOutIcon,
  MenuIcon,
  MoonIcon,
  RepeatIcon,
  SearchIcon,
  SettingsIcon,
  SunIcon } from
'lucide-react';
import { useTheme } from '../../contexts/ThemeContext';
import { roleConfigs, type RoleConfig } from '../../data/nav';
import { notifications } from '../../data/activity';
import { cn } from '../../utils/cn';
import { Avatar } from '../ui/Bits';
import { IconButton } from '../ui/Button';
import { Dropdown } from '../ui/Dropdown';
import { Input } from '../ui/Field';

export function Navbar({
  config,
  breadcrumb,
  onOpenDrawer




}: {config: RoleConfig;breadcrumb: string[];onOpenDrawer: () => void;}) {
  const { theme, toggleTheme } = useTheme();
  const navigate = useNavigate();
  const unread = notifications.filter((item) => item.unread).length;

  return (
    <header className="sticky top-0 z-30 flex h-14 shrink-0 items-center gap-3 border-b border-line bg-surface px-3 lg:px-5">
      <IconButton icon={MenuIcon} label="Abrir menu" onClick={onOpenDrawer} className="lg:hidden" />

      <nav aria-label="Localização" className="min-w-0 flex-1">
        <ol className="flex items-center gap-1.5 text-xs text-fg-subtle">
          <li className="hidden sm:block">{config.label}</li>
          <li aria-hidden="true" className="hidden sm:block">
            <ChevronRightIcon className="h-3 w-3" />
          </li>
          {breadcrumb.map((crumb, index) => {
            const last = index === breadcrumb.length - 1;
            return (
              <React.Fragment key={crumb}>
                <li className={cn('truncate', last && 'text-sm font-semibold text-fg')}>{crumb}</li>
                {!last ?
                <li aria-hidden="true">
                    <ChevronRightIcon className="h-3 w-3" />
                  </li> :
                null}
              </React.Fragment>);

          })}
        </ol>
      </nav>

      <div className="hidden w-56 md:block xl:w-72">
        <Input icon={SearchIcon} placeholder="Pesquisar cursos, alunos…" aria-label="Pesquisar" className="h-8 text-xs" />
      </div>

      <div className="flex items-center gap-0.5">
        <IconButton
          icon={theme === 'light' ? MoonIcon : SunIcon}
          label={theme === 'light' ? 'Modo escuro' : 'Modo claro'}
          onClick={toggleTheme} />
        

        <Dropdown
          width="w-72"
          header={
          <div className="flex items-center justify-between">
              <p className="text-xs font-semibold text-fg">Notificações</p>
              <span className="text-2xs text-fg-subtle tnum">{unread} não lidas</span>
            </div>
          }
          items={notifications.map((item) => ({
            id: item.id,
            label: item.title
          }))}
          trigger={({ toggle }) =>
          <span className="relative inline-flex">
              <IconButton icon={BellIcon} label="Notificações" onClick={toggle} />
              {unread > 0 ?
            <span
              aria-hidden="true"
              className="absolute right-1 top-1 h-1.5 w-1.5 rounded-full bg-danger ring-2 ring-[color:var(--surface)]" /> :

            null}
            </span>
          } />
        

        <Dropdown
          width="w-56"
          header={
          <div className="flex items-center gap-2">
              <Avatar name={config.user.name} size="sm" />
              <div className="min-w-0">
                <p className="truncate text-xs font-medium text-fg">{config.user.name}</p>
                <p className="truncate text-2xs text-fg-subtle">{config.user.email}</p>
              </div>
            </div>
          }
          items={[
          { id: 'profile', label: 'Meu perfil', icon: CircleUserRoundIcon },
          { id: 'settings', label: 'Configurações', icon: SettingsIcon },
          ...(Object.values(roleConfigs).
          filter((item) => item.role !== config.role).
          map((item) => ({
            id: `role-${item.role}`,
            label: `Ver como ${item.label}`,
            icon: RepeatIcon,
            onSelect: () => navigate(item.home)
          })) as {
            id: string;
            label: string;
            icon: typeof RepeatIcon;
            onSelect: () => void;
          }[]),
          { id: 'logout', label: 'Terminar sessão', icon: LogOutIcon, tone: 'danger' as const }]
          }
          trigger={({ toggle }) =>
          <button
            type="button"
            onClick={toggle}
            className="ml-1 flex items-center gap-1.5 rounded-md border border-line bg-surface px-1.5 py-1 transition-colors duration-150 ease-out hover:bg-surface-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[color:var(--ring)] focus-visible:ring-offset-2 focus-visible:ring-offset-[color:var(--surface)]">
            
              <Avatar name={config.user.name} size="sm" />
              <ChevronDownIcon className="h-3.5 w-3.5 text-fg-subtle" />
            </button>
          } />
        
      </div>
    </header>);

}