import React, { useState } from 'react';
import { DownloadIcon, FileTextIcon } from 'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { EnrollmentsChart, RevenueChart } from '../../components/dashboard/Charts';
import { Button } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { StatCard } from '../../components/ui/Bits';
import { Segmented } from '../../components/ui/Tabs';
import { Table, TBody, TD, TH, THead, TR } from '../../components/ui/Table';
import { popularCourses } from '../../data/courses';
import { formatMZN, formatNumber, formatPercent } from '../../utils/format';

const reportExports = [
{ id: 'r1', name: 'Inscrições por curso', period: 'Ago 2026', format: 'CSV' },
{ id: 'r2', name: 'Receita por método de pagamento', period: 'Ago 2026', format: 'CSV' },
{ id: 'r3', name: 'Conclusões e certificados', period: 'Jul 2026', format: 'PDF' },
{ id: 'r4', name: 'Comissões de formadores', period: 'Jul 2026', format: 'CSV' }];


export function AdminReports() {
  const [range, setRange] = useState('30d');

  return (
    <>
      <PageHeader
        title="Relatórios"
        actions={
        <>
            <Segmented
            value={range}
            onChange={setRange}
            items={[
            { id: '7d', label: '7 dias' },
            { id: '30d', label: '30 dias' },
            { id: '12m', label: '12 meses' }]
            } />
          
            <Button size="sm" icon={DownloadIcon}>
              Exportar
            </Button>
          </>
        } />
      

      <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <StatCard label="Inscrições" value={formatNumber(1091)} delta={9.6} deltaLabel="vs. período anterior" />
        <StatCard label="Conclusões" value={formatNumber(535)} delta={4.2} deltaLabel="49% das inscrições" />
        <StatCard label="Receita" value={formatMZN(682000)} delta={12.9} deltaLabel="vs. Julho" />
        <StatCard label="Cancelamentos" value="37" delta={-2.1} deltaLabel="3,4% da base" />
      </div>

      <div className="mt-3 grid grid-cols-1 gap-3 xl:grid-cols-2">
        <Section title="Receita" bodyClassName="px-2 py-3">
          <RevenueChart />
        </Section>
        <Section title="Inscrições e conclusões" bodyClassName="px-2 py-3">
          <EnrollmentsChart />
        </Section>
      </div>

      <div className="mt-3 grid grid-cols-1 gap-3 xl:grid-cols-3">
        <Section className="xl:col-span-2" title="Desempenho por curso" bodyClassName="p-0">
          <Table>
            <THead>
              <TR hoverable={false}>
                <TH>Curso</TH>
                <TH align="right">Inscrições</TH>
                <TH align="right">Receita</TH>
                <TH align="right">Variação</TH>
              </TR>
            </THead>
            <TBody>
              {popularCourses.map((course) =>
              <TR key={course.id}>
                  <TD className="max-w-[260px] truncate font-medium">{course.title}</TD>
                  <TD align="right" className="tnum text-fg-muted">
                    {formatNumber(course.enrollments)}
                  </TD>
                  <TD align="right" className="tnum">
                    {formatMZN(course.revenue, { compact: true })}
                  </TD>
                  <TD align="right" className={`tnum font-semibold ${course.trend >= 0 ? 'text-success' : 'text-danger'}`}>
                    {formatPercent(course.trend)}
                  </TD>
                </TR>
              )}
            </TBody>
          </Table>
        </Section>

        <Section title="Exportações" bodyClassName="p-0">
          <ul className="divide-y divide-line">
            {reportExports.map((item) =>
            <li key={item.id} className="flex items-center gap-2.5 px-4 py-2.5">
                <FileTextIcon className="h-4 w-4 shrink-0 text-fg-subtle" />
                <div className="min-w-0 flex-1">
                  <p className="truncate text-xs font-medium text-fg">{item.name}</p>
                  <p className="truncate text-2xs text-fg-subtle">
                    {item.period} · {item.format}
                  </p>
                </div>
                <Button size="xs" variant="ghost" icon={DownloadIcon}>
                  {item.format}
                </Button>
              </li>
            )}
          </ul>
        </Section>
      </div>
    </>);

}