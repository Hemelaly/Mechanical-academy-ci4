import React, { useMemo, useState } from 'react';
import { CheckIcon, DownloadIcon, MoreHorizontalIcon, SearchCheckIcon } from 'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { Toolbar } from '../../components/dashboard/Toolbar';
import { CertificateStatusBadge } from '../../components/dashboard/status';
import { Button, IconButton } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { Table, TBody, TD, TH, THead, TR } from '../../components/ui/Table';
import { Tabs } from '../../components/ui/Tabs';
import { Dropdown } from '../../components/ui/Dropdown';
import { certificates, type CertificateStatus } from '../../data/certificates';

export function AdminCertificates() {
  const [tab, setTab] = useState('todos');
  const [query, setQuery] = useState('');

  const filtered = useMemo(
    () =>
    certificates.filter((item) => {
      const matchesTab = tab === 'todos' || item.status === tab as CertificateStatus;
      const matchesQuery =
      query.trim() === '' ||
      item.student.toLowerCase().includes(query.toLowerCase()) ||
      item.code.toLowerCase().includes(query.toLowerCase());
      return matchesTab && matchesQuery;
    }),
    [tab, query]
  );

  return (
    <>
      <PageHeader
        title="Certificados"
        actions={
        <>
            <Button size="sm" icon={SearchCheckIcon}>
              Validar código
            </Button>
            <Button size="sm" variant="primary" icon={CheckIcon}>
              Emitir pendentes
            </Button>
          </>
        } />
      

      <Section bodyClassName="p-0">
        <div className="px-4 pt-3">
          <Tabs
            value={tab}
            onChange={setTab}
            items={[
            { id: 'todos', label: 'Todos', count: certificates.length },
            { id: 'emitido', label: 'Emitidos', count: certificates.filter((c) => c.status === 'emitido').length },
            { id: 'pendente', label: 'Pendentes', count: certificates.filter((c) => c.status === 'pendente').length },
            { id: 'revogado', label: 'Revogados', count: certificates.filter((c) => c.status === 'revogado').length }]
            } />
          
        </div>

        <Toolbar
          placeholder="Código ou aluno"
          query={query}
          onQueryChange={setQuery}
          filters={[
          {
            id: 'curso',
            ariaLabel: 'Curso',
            options: [
            { value: 'todos', label: 'Todos os cursos' },
            { value: 'excel', label: 'Excel' },
            { value: 'web', label: 'Programação Web' }]

          }]
          } />
        

        <Table>
          <THead>
            <TR hoverable={false}>
              <TH>Código</TH>
              <TH>Aluno</TH>
              <TH>Curso</TH>
              <TH align="right">Nota</TH>
              <TH>Estado</TH>
              <TH align="right">Emissão</TH>
              <TH align="right">Acções</TH>
            </TR>
          </THead>
          <TBody>
            {filtered.map((item) =>
            <TR key={item.id}>
                <TD className="font-medium tnum">{item.code}</TD>
                <TD className="text-fg-muted">{item.student}</TD>
                <TD className="max-w-[220px]">
                  <p className="truncate">{item.course}</p>
                  <p className="truncate text-2xs text-fg-subtle">{item.instructor}</p>
                </TD>
                <TD align="right" className="tnum">
                  {item.grade}%
                </TD>
                <TD>
                  <CertificateStatusBadge status={item.status} />
                </TD>
                <TD align="right" className="text-2xs text-fg-subtle">
                  {item.issued}
                </TD>
                <TD align="right">
                  <div className="flex items-center justify-end gap-0.5">
                    <IconButton icon={DownloadIcon} label={`Descarregar ${item.code}`} />
                    <Dropdown
                    items={[
                    { id: 'ver', label: 'Pré-visualizar' },
                    { id: 'reenviar', label: 'Reenviar por email' },
                    { id: 'revogar', label: 'Revogar', tone: 'danger' }]
                    }
                    trigger={({ toggle }) =>
                    <IconButton icon={MoreHorizontalIcon} label="Mais acções" onClick={toggle} />
                    } />
                  
                  </div>
                </TD>
              </TR>
            )}
            {filtered.length === 0 ?
            <TR hoverable={false}>
                <TD colSpan={7} className="py-10 text-center text-xs text-fg-muted">
                  Nenhum certificado encontrado.
                </TD>
              </TR> :
            null}
          </TBody>
        </Table>
      </Section>
    </>);

}