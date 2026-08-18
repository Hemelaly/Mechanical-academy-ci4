import React, { useEffect, useState } from 'react';
import { Outlet, useLocation } from 'react-router-dom';
import { AnimatePresence, motion } from 'framer-motion';
import { XIcon } from 'lucide-react';
import { roleConfigs, type Role } from '../../data/nav';
import { cn } from '../../utils/cn';
import { IconButton } from '../ui/Button';
import { Navbar } from './Navbar';
import { SidebarContent } from './Sidebar';

/** Route path → breadcrumb trail for paths that are not top-level nav items. */
const extraBreadcrumbs: Record<string, string[]> = {
  '/instrutor/cursos/novo': ['Meus cursos', 'Novo curso'],
  '/design-system': ['Design system']
};

function useBreadcrumb(role: Role): string[] {
  const { pathname } = useLocation();
  const config = roleConfigs[role];
  if (extraBreadcrumbs[pathname]) return extraBreadcrumbs[pathname];
  const match = [...config.nav, ...config.secondary].
  filter((item) => pathname === item.to || pathname.startsWith(`${item.to}/`)).
  sort((a, b) => b.to.length - a.to.length)[0];
  return [match?.label ?? 'Painel'];
}

export function AppShell({ role }: {role: Role;}) {
  const config = roleConfigs[role];
  const [collapsed, setCollapsed] = useState(false);
  const [drawerOpen, setDrawerOpen] = useState(false);
  const breadcrumb = useBreadcrumb(role);
  const { pathname } = useLocation();

  useEffect(() => {
    setDrawerOpen(false);
  }, [pathname]);

  return (
    <div className="flex h-screen w-full overflow-hidden bg-canvas">
      {/* Desktop sidebar */}
      <aside
        className={cn(
          'hidden shrink-0 border-r border-line lg:block',
          'transition-[width] duration-200 ease-out',
          collapsed ? 'w-14' : 'w-60'
        )}>
        
        <SidebarContent config={config} collapsed={collapsed} onToggleCollapse={() => setCollapsed((v) => !v)} />
      </aside>

      {/* Mobile drawer */}
      <AnimatePresence>
        {drawerOpen ?
        <div className="fixed inset-0 z-50 lg:hidden">
            <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.16, ease: [0.23, 1, 0.32, 1] }}
            className="absolute inset-0 bg-[#07090d]/50"
            onClick={() => setDrawerOpen(false)} />
          
            <motion.div
            initial={{ x: -280 }}
            animate={{ x: 0 }}
            exit={{ x: -280 }}
            transition={{ duration: 0.24, ease: [0.23, 1, 0.32, 1] }}
            className="absolute inset-y-0 left-0 w-64 border-r border-line shadow-pop">
            
              <SidebarContent config={config} collapsed={false} />
              <div className="absolute right-2 top-3.5">
                <IconButton icon={XIcon} label="Fechar menu" onClick={() => setDrawerOpen(false)} />
              </div>
            </motion.div>
          </div> :
        null}
      </AnimatePresence>

      <div className="flex min-w-0 flex-1 flex-col">
        <Navbar config={config} breadcrumb={breadcrumb} onOpenDrawer={() => setDrawerOpen(true)} />
        <main className="scroll-area flex-1 overflow-y-auto">
          <div className="mx-auto w-full max-w-[1180px] px-4 py-5 lg:px-6 lg:py-6">
            <Outlet />
          </div>
        </main>
      </div>
    </div>);

}