import React, { useMemo, useState } from 'react';
import { MailIcon, MoreHorizontalIcon } from 'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { Toolbar } from '../../components/dashboard/Toolbar';
import { PersonStatusBadge } from '../../components/dashboard/status';
import { IconButton } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { Avatar, ProgressBar } from '../../components/ui/Bits';
import { Table, TBody, TD, TH, THead, TR } from '../../components/ui/Table';
import { Dropdown } from '../../components/ui/Dropdown';
import { Badge } from '../../components/ui/Badge';
import { students } from '../../data/people';

const rows = students.slice(0, 7).map((student, index) => ({
  ...student,
  course: index % 2 === 0 ? 'Excel Avançado' : 'Marketing Digital',
  late: student.progress < 40
}));

export function InstructorStudents() {
  const [query, setQuery] = useState('');
  const [course, setCourse] = useState('todos');

  const filtered = useMemo(
    () =>
    rows.filter((row) => {
      const matchesCourse = course === 'todos' || row.course === course;
      const matchesQuery = query.trim() === '' || row.name.toLowerCase().includes(query.toLowerCase());
      return matchesCourse && matchesQuery;
    }),
    [query, course]
  );

  return (
    <>
      <PageHeader title="Alunos" />

      <Section bodyClassName="p-0">
        <Toolbar
          placeholder="Pesquisar aluno"
          query={query}
          onQueryChange={setQuery}
          filters={[
          {
            id: 'curso',
            ariaLabel: 'Curso',
            width: 'w-44',
            value: course,
            onChange: setCourse,
            options: [
            { value: 'todos', label: 'Todos os cursos' },
            { value: 'Excel Avançado', label: 'Excel Avançado' },
            { value: 'Marketing Digital', label: 'Marketing Digital' }]

          }]
          } />
        

        <Table>
          <THead>
            <TR hoverable={false}>
              <TH>Aluno</TH>
              <TH>Curso</TH>
              <TH>Progresso</TH>
              <TH>Estado</TH>
              <TH align="right">Actividade</TH>
              <TH align="right">Acções</TH>
            </TR>
          </THead>
          <TBody>
            {filtered.map((row) =>
            <TR key={row.id}>
                <TD>
                  <div className="flex items-center gap-2.5">
                    <Avatar name={row.name} size="sm" />
                    <div className="min-w-0">
                      <p className="truncate font-medium">{row.name}</p>
                      <p className="truncate text-2xs text-fg-subtle">{row.email}</p>
                    </div>
                  </div>
                </TD>
                <TD className="text-fg-muted">{row.course}</TD>
                <TD>
                  <ProgressBar value={row.progress} className="w-24" />
                </TD>
                <TD>
                  {row.late ?
                <Badge tone="warn" dot>
                      Em atraso
                    </Badge> :

                <PersonStatusBadge status={row.status} />
                }
                </TD>
                <TD align="right" className="text-2xs text-fg-subtle">
                  {row.lastActive}
                </TD>
                <TD align="right">
                  <div className="flex items-center justify-end gap-0.5">
                    <IconButton icon={MailIcon} label={`Mensagem para ${row.name}`} />
                    <Dropdown
                    items={[
                    { id: 'progresso', label: 'Ver progresso' },
                    { id: 'nota', label: 'Lançar nota' },
                    { id: 'remover', label: 'Remover do curso', tone: 'danger' }]
                    }
                    trigger={({ toggle }) =>
                    <IconButton icon={MoreHorizontalIcon} label="Mais acções" onClick={toggle} />
                    } />
                  
                  </div>
                </TD>
              </TR>
            )}
          </TBody>
        </Table>
      </Section>
    </>);

}