import React, { useMemo, useState } from 'react';
import {
  BanknoteIcon,
  ClockIcon,
  DownloadIcon,
  ReceiptIcon,
  SearchIcon,
  SlidersHorizontalIcon,
  TrendingUpIcon } from
'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { PaymentStatusBadge } from '../../components/dashboard/status';
import { Button } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { StatCard } from '../../components/ui/Bits';
import { Input, Select } from '../../components/ui/Field';
import { Table, TBody, TD, TH, THead, TR } from '../../components/ui/Table';
import { Segmented, Tabs } from '../../components/ui/Tabs';
import { Pagination } from '../../components/ui/Pagination';
import { payoutBreakdown, transactions, type PaymentStatus } from '../../data/finance';
import { formatMZN, formatNumber } from '../../utils/format';

export function AdminFinance() {
  const [tab, setTab] = useState('todas');
  const [range, setRange] = useState('mes');
  const [query, setQuery] = useState('');
  const [page, setPage] = useState(1);
  const pageSize = 6;

  const filtered = useMemo(
    () =>
    transactions.filter((item) => {
      const matchesTab = tab === 'todas' || item.status === tab as PaymentStatus;
      const matchesQuery =
      query.trim() === '' ||
      item.student.toLowerCase().includes(query.toLowerCase()) ||
      item.reference.toLowerCase().includes(query.toLowerCase());
      return matchesTab && matchesQuery;
    }),
    [tab, query]
  );

  const pageCount = Math.max(1, Math.ceil(filtered.length / pageSize));
  const visible = filtered.slice((page - 1) * pageSize, page * pageSize);

  return (
    <>
      <PageHeader
        title="Financeiro"
        description="Pagamentos, reembolsos e transferências para formadores"
        actions={
        <>
            <Segmented
            value={range}
            onChange={setRange}
            items={[
            { id: 'semana', label: 'Semana' },
            { id: 'mes', label: 'Mês' },
            { id: 'ano', label: 'Ano' }]
            } />
          
            <Button size="sm" icon={DownloadIcon}>
              Exportar CSV
            </Button>
          </>
        } />
      

      <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <StatCard label="Receita bruta" value={formatMZN(682000)} delta={12.9} deltaLabel="vs. Julho" icon={BanknoteIcon} />
        <StatCard label="Líquido da plataforma" value={formatMZN(204600)} delta={11.2} deltaLabel="30% de comissão" icon={TrendingUpIcon} />
        <StatCard label="A aguardar confirmação" value={formatMZN(29600)} deltaLabel="4 transacções" delta={0} icon={ClockIcon} />
        <StatCard label="Reembolsos" value={formatMZN(8400)} delta={-4.5} deltaLabel="3 pedidos" icon={ReceiptIcon} />
      </div>

      <div className="mt-3 grid grid-cols-1 gap-3 xl:grid-cols-3">
        <Section className="xl:col-span-2" title="Transacções" bodyClassName="p-0">
          <div className="px-4 pt-3">
            <Tabs
              items={[
              { id: 'todas', label: 'Todas', count: transactions.length },
              { id: 'pago', label: 'Pagas', count: transactions.filter((t) => t.status === 'pago').length },
              { id: 'pendente', label: 'Pendentes', count: transactions.filter((t) => t.status === 'pendente').length },
              { id: 'falhado', label: 'Falhadas', count: transactions.filter((t) => t.status === 'falhado').length }]
              }
              value={tab}
              onChange={(id) => {
                setTab(id);
                setPage(1);
              }} />
            
          </div>

          <div className="flex flex-wrap items-center gap-2 border-b border-line px-4 py-2.5">
            <div className="w-full sm:w-56">
              <Input
                icon={SearchIcon}
                placeholder="Referência ou aluno"
                aria-label="Pesquisar transacções"
                value={query}
                onChange={(event) => {
                  setQuery(event.target.value);
                  setPage(1);
                }}
                className="h-8 text-xs" />
              
            </div>
            <div className="w-36">
              <Select
                aria-label="Método de pagamento"
                className="h-8 text-xs"
                options={[
                { value: 'todos', label: 'Todos os métodos' },
                { value: 'mpesa', label: 'M-Pesa' },
                { value: 'emola', label: 'e-Mola' },
                { value: 'transferencia', label: 'Transferência' },
                { value: 'cartao', label: 'Cartão' }]
                } />
              
            </div>
            <Button size="sm" icon={SlidersHorizontalIcon} className="ml-auto">
              Filtros
            </Button>
          </div>

          <Table>
            <THead>
              <TR hoverable={false}>
                <TH>Referência</TH>
                <TH>Aluno / Entidade</TH>
                <TH>Método</TH>
                <TH align="right">Valor (MZN)</TH>
                <TH>Estado</TH>
                <TH align="right">Data</TH>
              </TR>
            </THead>
            <TBody>
              {visible.map((item) =>
              <TR key={item.id}>
                  <TD className="font-medium tnum">{item.reference}</TD>
                  <TD className="max-w-[220px]">
                    <p className="truncate">{item.student}</p>
                    <p className="truncate text-2xs text-fg-subtle">{item.course}</p>
                  </TD>
                  <TD className="text-fg-muted">{item.method}</TD>
                  <TD align="right" className="font-medium tnum">
                    {formatNumber(item.amount)}
                  </TD>
                  <TD>
                    <PaymentStatusBadge status={item.status} />
                  </TD>
                  <TD align="right" className="text-2xs text-fg-subtle">
                    {item.date}
                  </TD>
                </TR>
              )}
              {visible.length === 0 ?
              <TR hoverable={false}>
                  <TD colSpan={6} className="py-10 text-center text-xs text-fg-muted">
                    Nenhuma transacção encontrada.
                  </TD>
                </TR> :
              null}
            </TBody>
          </Table>

          <div className="border-t border-line px-4 py-2.5">
            <Pagination
              page={page}
              pageCount={pageCount}
              total={filtered.length}
              pageSize={pageSize}
              onPageChange={setPage} />
            
          </div>
        </Section>

        <div className="space-y-3">
          <Section title="Por método de pagamento" description="Últimos 30 dias">
            <ul className="space-y-3">
              {payoutBreakdown.map((item) =>
              <li key={item.label}>
                  <div className="flex items-baseline justify-between gap-2">
                    <span className="truncate text-xs font-medium text-fg">{item.label}</span>
                    <span className="shrink-0 text-xs text-fg-muted tnum">{formatNumber(item.amount)}</span>
                  </div>
                  <div className="mt-1.5 h-1.5 w-full overflow-hidden rounded-md bg-surface-3">
                    <div className="h-full rounded-md bg-accent" style={{ width: `${item.share}%` }} />
                  </div>
                </li>
              )}
            </ul>
          </Section>

          <Section title="Próxima transferência" description="Formadores · 15 Ago 2026">
            <dl className="space-y-2 text-xs">
              <div className="flex items-center justify-between gap-2">
                <dt className="text-fg-muted">Total a transferir</dt>
                <dd className="font-semibold text-fg tnum">{formatMZN(477400)}</dd>
              </div>
              <div className="flex items-center justify-between gap-2">
                <dt className="text-fg-muted">Formadores</dt>
                <dd className="text-fg tnum">18</dd>
              </div>
              <div className="flex items-center justify-between gap-2">
                <dt className="text-fg-muted">Via M-Pesa</dt>
                <dd className="text-fg tnum">16</dd>
              </div>
            </dl>
            <Button variant="primary" size="sm" block className="mt-3">
              Aprovar transferências
            </Button>
          </Section>
        </div>
      </div>
    </>);

}