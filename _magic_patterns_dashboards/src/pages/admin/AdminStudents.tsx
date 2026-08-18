import React, { useMemo, useState } from 'react';
import { DownloadIcon, MailIcon, MoreHorizontalIcon, PlusIcon, UserRoundXIcon } from 'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { Toolbar } from '../../components/dashboard/Toolbar';
import { PersonStatusBadge } from '../../components/dashboard/status';
import { Button, IconButton } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { Avatar, ProgressBar } from '../../components/ui/Bits';
import { Table, TBody, TD, TH, THead, TR } from '../../components/ui/Table';
import { Tabs } from '../../components/ui/Tabs';
import { Pagination } from '../../components/ui/Pagination';
import { Dropdown } from '../../components/ui/Dropdown';
import { students, type PersonStatus } from '../../data/people';

export function AdminStudents() {
  const [tab, setTab] = useState('todos');
  const [query, setQuery] = useState('');
  const [page, setPage] = useState(1);
  const pageSize = 6;

  const filtered = useMemo(
    () =>
    students.filter((student) => {
      const matchesTab = tab === 'todos' || student.status === tab as PersonStatus;
      const matchesQuery =
      query.trim() === '' ||
      student.name.toLowerCase().includes(query.toLowerCase()) ||
      student.email.toLowerCase().includes(query.toLowerCase());
      return matchesTab && matchesQuery;
    }),
    [tab, query]
  );

  const pageCount = Math.max(1, Math.ceil(filtered.length / pageSize));
  const visible = filtered.slice((page - 1) * pageSize, page * pageSize);

  return (
    <>
      <PageHeader
        title="Alunos"
        actions={
        <>
            <Button size="sm" icon={DownloadIcon}>
              Exportar
            </Button>
            <Button size="sm" variant="primary" icon={PlusIcon}>
              Adicionar aluno
            </Button>
          </>
        } />
      

      <Section bodyClassName="p-0">
        <div className="px-4 pt-3">
          <Tabs
            value={tab}
            onChange={(id) => {
              setTab(id);
              setPage(1);
            }}
            items={[
            { id: 'todos', label: 'Todos', count: students.length },
            { id: 'activo', label: 'Activos', count: students.filter((s) => s.status === 'activo').length },
            { id: 'pendente', label: 'Pendentes', count: students.filter((s) => s.status === 'pendente').length },
            { id: 'suspenso', label: 'Suspensos', count: students.filter((s) => s.status === 'suspenso').length }]
            } />
          
        </div>

        <Toolbar
          placeholder="Pesquisar aluno ou email"
          query={query}
          onQueryChange={(value) => {
            setQuery(value);
            setPage(1);
          }}
          filters={[
          {
            id: 'curso',
            ariaLabel: 'Curso',
            options: [
            { value: 'todos', label: 'Todos os cursos' },
            { value: 'excel', label: 'Excel Avançado' },
            { value: 'marketing', label: 'Marketing Digital' },
            { value: 'web', label: 'Programação Web' }]

          }]
          } />
        

        <Table>
          <THead>
            <TR hoverable={false}>
              <TH>Aluno</TH>
              <TH>Contacto</TH>
              <TH align="right">Cursos</TH>
              <TH>Progresso</TH>
              <TH>Estado</TH>
              <TH align="right">Actividade</TH>
              <TH align="right">Acções</TH>
            </TR>
          </THead>
          <TBody>
            {visible.map((student) =>
            <TR key={student.id}>
                <TD>
                  <div className="flex items-center gap-2.5">
                    <Avatar name={student.name} size="sm" />
                    <span className="truncate font-medium">{student.name}</span>
                  </div>
                </TD>
                <TD className="text-fg-muted">
                  <p className="truncate text-xs">{student.email}</p>
                  <p className="truncate text-2xs text-fg-subtle tnum">{student.phone}</p>
                </TD>
                <TD align="right" className="tnum text-fg-muted">
                  {student.courses}
                </TD>
                <TD>
                  <ProgressBar value={student.progress} className="w-24" />
                </TD>
                <TD>
                  <PersonStatusBadge status={student.status} />
                </TD>
                <TD align="right" className="text-2xs text-fg-subtle">
                  {student.lastActive}
                </TD>
                <TD align="right">
                  <div className="flex items-center justify-end gap-0.5">
                    <IconButton icon={MailIcon} label={`Enviar email a ${student.name}`} />
                    <Dropdown
                    items={[
                    { id: 'ver', label: 'Ver perfil' },
                    { id: 'inscrever', label: 'Inscrever em curso' },
                    { id: 'suspender', label: 'Suspender', icon: UserRoundXIcon, tone: 'danger' }]
                    }
                    trigger={({ toggle }) =>
                    <IconButton icon={MoreHorizontalIcon} label="Mais acções" onClick={toggle} />
                    } />
                  
                  </div>
                </TD>
              </TR>
            )}
            {visible.length === 0 ?
            <TR hoverable={false}>
                <TD colSpan={7} className="py-10 text-center text-xs text-fg-muted">
                  Nenhum aluno encontrado.
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
    </>);

}