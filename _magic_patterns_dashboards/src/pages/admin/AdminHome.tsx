import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import {
  ArrowRightIcon,
  BookOpenIcon,
  DownloadIcon,
  PlusIcon,
  TrendingUpIcon,
  UsersIcon,
  WalletIcon } from
'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { ActivityFeed } from '../../components/dashboard/ActivityFeed';
import { EnrollmentsChart, RevenueChart } from '../../components/dashboard/Charts';
import { Button } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { StatCard, StatCardSkeleton } from '../../components/ui/Bits';
import { Segmented } from '../../components/ui/Tabs';
import { adminActivity, adminQuickActions } from '../../data/activity';
import { popularCourses } from '../../data/courses';
import { formatMZN, formatNumber, formatPercent } from '../../utils/format';

export function AdminHome() {
  const [range, setRange] = useState('30d');
  const [loading, setLoading] = useState(false);

  return (
    <>
      <PageHeader
        title="Painel"
        description="Visão geral da plataforma — 13 de Agosto de 2026"
        actions={
        <>
            <Segmented
            value={range}
            onChange={(id) => {
              setRange(id);
              setLoading(true);
              window.setTimeout(() => setLoading(false), 700);
            }}
            items={[
            { id: '7d', label: '7 dias' },
            { id: '30d', label: '30 dias' },
            { id: '12m', label: '12 meses' }]
            } />
          
            <Button size="sm" icon={DownloadIcon}>
              Exportar
            </Button>
            <Button size="sm" variant="primary" icon={PlusIcon}>
              Novo curso
            </Button>
          </>
        } />
      

      <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        {loading ?
        <>
            <StatCardSkeleton />
            <StatCardSkeleton />
            <StatCardSkeleton />
            <StatCardSkeleton />
          </> :

        <>
            <StatCard
            label="Receita do mês"
            value={formatMZN(682000)}
            delta={12.9}
            deltaLabel="vs. Julho"
            icon={WalletIcon} />
          
            <StatCard
            label="Alunos activos"
            value={formatNumber(2874)}
            delta={6.4}
            deltaLabel="vs. mês anterior"
            icon={UsersIcon} />
          
            <StatCard
            label="Cursos publicados"
            value="86"
            delta={3.1}
            deltaLabel="4 em revisão"
            icon={BookOpenIcon} />
          
            <StatCard
            label="Taxa de conclusão"
            value="61,2%"
            delta={-1.8}
            deltaLabel="vs. mês anterior"
            icon={TrendingUpIcon} />
          
          </>
        }
      </div>

      <div className="mt-3 grid grid-cols-1 gap-3 xl:grid-cols-3">
        <Section
          className="xl:col-span-2"
          title="Receita"
          description="Receita mensal vs. meta (MZN)"
          action={
          <div className="hidden items-center gap-3 sm:flex">
              <span className="flex items-center gap-1.5 text-2xs text-fg-muted">
                <span className="h-1.5 w-1.5 rounded-full bg-accent" /> Receita
              </span>
              <span className="flex items-center gap-1.5 text-2xs text-fg-muted">
                <span className="h-px w-3 bg-line-strong" /> Meta
              </span>
            </div>
          }
          bodyClassName="px-2 py-3">
          
          <RevenueChart />
        </Section>

        <Section title="Inscrições" description="Últimas 6 semanas" bodyClassName="px-2 py-3">
          <EnrollmentsChart />
        </Section>
      </div>

      <div className="mt-3 grid grid-cols-1 gap-3 xl:grid-cols-3">
        <Section
          className="xl:col-span-2"
          title="Cursos populares"
          description="Por inscrições no período"
          action={
          <Button variant="ghost" size="xs" iconRight={ArrowRightIcon} className="text-accent hover:text-accent">
              Ver todos
            </Button>
          }
          bodyClassName="p-0">
          
          <ul className="divide-y divide-line">
            {popularCourses.map((course, index) =>
            <li key={course.id} className="flex items-center gap-3 px-4 py-2.5">
                <span className="w-4 shrink-0 text-2xs font-semibold text-fg-subtle tnum">{index + 1}</span>
                <div className="min-w-0 flex-1">
                  <p className="truncate text-sm font-medium text-fg">{course.title}</p>
                  <p className="text-2xs text-fg-subtle tnum">{formatNumber(course.enrollments)} inscrições</p>
                </div>
                <span className="shrink-0 text-sm font-medium text-fg tnum">
                  {formatMZN(course.revenue, { compact: true })}
                </span>
                <span
                className={`w-14 shrink-0 text-right text-2xs font-semibold tnum ${
                course.trend >= 0 ? 'text-success' : 'text-danger'}`
                }>
                
                  {formatPercent(course.trend)}
                </span>
              </li>
            )}
          </ul>
        </Section>

        <div className="space-y-3">
          <Section title="Actividade recente" bodyClassName="p-0">
            <ActivityFeed items={adminActivity.slice(0, 5)} />
          </Section>

          <Section title="Acções rápidas" bodyClassName="p-2">
            <ul className="grid grid-cols-2 gap-1.5">
              {adminQuickActions.map((action) =>
              <li key={action.id}>
                  <Link
                  to={action.to}
                  className="block rounded-md border border-line px-2.5 py-2 transition-[background-color,border-color] duration-150 ease-out hover:border-accent hover:bg-accent-soft focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[color:var(--ring)]">
                  
                    <span className="block truncate text-xs font-medium text-fg">{action.label}</span>
                    <span className="block truncate text-2xs text-fg-subtle">{action.description}</span>
                  </Link>
                </li>
              )}
            </ul>
          </Section>
        </div>
      </div>
    </>);

}