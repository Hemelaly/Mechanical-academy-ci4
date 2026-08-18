import React from 'react';
import { BanknoteIcon, ClockIcon, DownloadIcon, TrendingUpIcon, WalletIcon } from 'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { EarningsChart } from '../../components/dashboard/Charts';
import { PaymentStatusBadge } from '../../components/dashboard/status';
import { Button } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { StatCard } from '../../components/ui/Bits';
import { Table, TBody, TD, TH, THead, TR } from '../../components/ui/Table';
import { formatMZN, formatNumber } from '../../utils/format';
import type { PaymentStatus } from '../../data/finance';

const payouts: {id: string;reference: string;period: string;method: string;amount: number;status: PaymentStatus;}[] = [
{ id: 'p1', reference: 'PAY-2026-08A', period: '01–15 Ago', method: 'M-Pesa · 84 512 8830', amount: 18200, status: 'pago' },
{ id: 'p2', reference: 'PAY-2026-07B', period: '16–31 Jul', method: 'M-Pesa · 84 512 8830', amount: 31400, status: 'pago' },
{ id: 'p3', reference: 'PAY-2026-07A', period: '01–15 Jul', method: 'M-Pesa · 84 512 8830', amount: 32000, status: 'pago' },
{ id: 'p4', reference: 'PAY-2026-08B', period: '16–31 Ago', method: 'M-Pesa · 84 512 8830', amount: 24300, status: 'pendente' }];


export function InstructorEarnings() {
  return (
    <>
      <PageHeader
        title="Ganhos"
        actions={
        <Button size="sm" icon={DownloadIcon}>
            Extracto
          </Button>
        } />
      

      <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <StatCard label="Este mês" value={formatMZN(71900)} delta={13.4} deltaLabel="líquido" icon={WalletIcon} />
        <StatCard label="Saldo disponível" value={formatMZN(24300)} deltaLabel="transferência a 15 Ago" icon={ClockIcon} />
        <StatCard label="Total recebido" value={formatMZN(314800)} delta={8.9} deltaLabel="12 meses" icon={BanknoteIcon} />
        <StatCard label="Comissão média" value="70%" deltaLabel="por venda" icon={TrendingUpIcon} />
      </div>

      <div className="mt-3 grid grid-cols-1 gap-3 xl:grid-cols-3">
        <Section className="xl:col-span-2" title="Evolução" description="Últimos 6 meses (MZN)" bodyClassName="px-2 py-3">
          <EarningsChart />
        </Section>

        <Section title="Por curso" description="Últimos 30 dias">
          <ul className="space-y-3">
            {[
            { label: 'Excel Avançado para Gestão', amount: 38200, share: 53 },
            { label: 'Marketing Digital para PMEs', amount: 26400, share: 37 },
            { label: 'Gestão de Recursos Humanos', amount: 7300, share: 10 }].
            map((item) =>
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
      </div>

      <Section className="mt-3" title="Transferências" bodyClassName="p-0">
        <Table>
          <THead>
            <TR hoverable={false}>
              <TH>Referência</TH>
              <TH>Período</TH>
              <TH>Método</TH>
              <TH align="right">Valor (MZN)</TH>
              <TH>Estado</TH>
            </TR>
          </THead>
          <TBody>
            {payouts.map((item) =>
            <TR key={item.id}>
                <TD className="font-medium tnum">{item.reference}</TD>
                <TD className="text-fg-muted">{item.period}</TD>
                <TD className="text-fg-muted tnum">{item.method}</TD>
                <TD align="right" className="font-medium tnum">
                  {formatNumber(item.amount)}
                </TD>
                <TD>
                  <PaymentStatusBadge status={item.status} />
                </TD>
              </TR>
            )}
          </TBody>
        </Table>
      </Section>
    </>);

}