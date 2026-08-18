import React, { useMemo, useState } from 'react';
import { Link } from 'react-router-dom';
import { MoreHorizontalIcon, PencilIcon, PlusIcon, StarIcon, UsersIcon } from 'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { Toolbar } from '../../components/dashboard/Toolbar';
import { CourseStatusBadge } from '../../components/dashboard/status';
import { Button, IconButton } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { Table, TBody, TD, TH, THead, TR } from '../../components/ui/Table';
import { Tabs } from '../../components/ui/Tabs';
import { Dropdown } from '../../components/ui/Dropdown';
import { courses, type CourseStatus } from '../../data/courses';
import { formatNumber } from '../../utils/format';

const myCourses = courses.filter((course) => course.instructor === 'Ana Chirindza' || course.id === 'c6');

export function InstructorCourses() {
  const [tab, setTab] = useState('todos');
  const [query, setQuery] = useState('');

  const filtered = useMemo(
    () =>
    myCourses.filter((course) => {
      const matchesTab = tab === 'todos' || course.status === tab as CourseStatus;
      const matchesQuery = query.trim() === '' || course.title.toLowerCase().includes(query.toLowerCase());
      return matchesTab && matchesQuery;
    }),
    [tab, query]
  );

  return (
    <>
      <PageHeader
        title="Meus cursos"
        actions={
        <Link to="/instrutor/cursos/novo">
            <Button size="sm" variant="primary" icon={PlusIcon}>
              Criar curso
            </Button>
          </Link>
        } />
      

      <Section bodyClassName="p-0">
        <div className="px-4 pt-3">
          <Tabs
            value={tab}
            onChange={setTab}
            items={[
            { id: 'todos', label: 'Todos', count: myCourses.length },
            { id: 'publicado', label: 'Publicados', count: myCourses.filter((c) => c.status === 'publicado').length },
            { id: 'rascunho', label: 'Rascunhos', count: myCourses.filter((c) => c.status === 'rascunho').length }]
            } />
          
        </div>

        <Toolbar placeholder="Pesquisar curso" query={query} onQueryChange={setQuery} />

        <Table>
          <THead>
            <TR hoverable={false}>
              <TH>Curso</TH>
              <TH align="right">Alunos</TH>
              <TH align="right">Preço (MZN)</TH>
              <TH align="right">Avaliação</TH>
              <TH>Estado</TH>
              <TH align="right">Actualizado</TH>
              <TH align="right">Acções</TH>
            </TR>
          </THead>
          <TBody>
            {filtered.map((course) =>
            <TR key={course.id}>
                <TD className="max-w-[260px]">
                  <p className="truncate font-medium">{course.title}</p>
                  <p className="truncate text-2xs text-fg-subtle">{course.category}</p>
                </TD>
                <TD align="right" className="tnum text-fg-muted">
                  {formatNumber(course.students)}
                </TD>
                <TD align="right" className="tnum">
                  {formatNumber(course.price)}
                </TD>
                <TD align="right">
                  {course.rating > 0 ?
                <span className="inline-flex items-center gap-1 text-fg-muted tnum">
                      <StarIcon className="h-3 w-3 text-warn" />
                      {course.rating.toFixed(1).replace('.', ',')}
                    </span> :

                <span className="text-fg-subtle">—</span>
                }
                </TD>
                <TD>
                  <CourseStatusBadge status={course.status} />
                </TD>
                <TD align="right" className="text-2xs text-fg-subtle">
                  {course.updated}
                </TD>
                <TD align="right">
                  <div className="flex items-center justify-end gap-0.5">
                    <IconButton icon={PencilIcon} label={`Editar ${course.title}`} />
                    <Dropdown
                    items={[
                    { id: 'alunos', label: 'Ver alunos', icon: UsersIcon },
                    { id: 'duplicar', label: 'Duplicar' },
                    { id: 'arquivar', label: 'Arquivar', tone: 'danger' }]
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