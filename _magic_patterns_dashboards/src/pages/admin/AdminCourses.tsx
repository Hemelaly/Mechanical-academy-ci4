import React, { useMemo, useState } from 'react';
import {
  CopyIcon,
  DownloadIcon,
  EyeIcon,
  MoreHorizontalIcon,
  PencilIcon,
  PlusIcon,
  SearchIcon,
  SlidersHorizontalIcon,
  StarIcon,
  Trash2Icon } from
'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { CourseStatusBadge } from '../../components/dashboard/status';
import { Button, IconButton } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { Input, Select } from '../../components/ui/Field';
import { Table, TBody, TD, TH, THead, TR } from '../../components/ui/Table';
import { Tabs } from '../../components/ui/Tabs';
import { Pagination } from '../../components/ui/Pagination';
import { Dropdown } from '../../components/ui/Dropdown';
import { Modal } from '../../components/ui/Feedback';
import { courses, type CourseStatus } from '../../data/courses';
import { formatNumber } from '../../utils/format';

const tabs = [
{ id: 'todos', label: 'Todos', count: courses.length },
{ id: 'publicado', label: 'Publicados', count: courses.filter((c) => c.status === 'publicado').length },
{ id: 'revisao', label: 'Em revisão', count: courses.filter((c) => c.status === 'revisao').length },
{ id: 'rascunho', label: 'Rascunhos', count: courses.filter((c) => c.status === 'rascunho').length }];


export function AdminCourses() {
  const [tab, setTab] = useState('todos');
  const [query, setQuery] = useState('');
  const [category, setCategory] = useState('todas');
  const [page, setPage] = useState(1);
  const [confirmDelete, setConfirmDelete] = useState<string | null>(null);
  const pageSize = 6;

  const filtered = useMemo(() => {
    return courses.filter((course) => {
      const matchesTab = tab === 'todos' || course.status === tab as CourseStatus;
      const matchesQuery =
      query.trim() === '' ||
      course.title.toLowerCase().includes(query.toLowerCase()) ||
      course.instructor.toLowerCase().includes(query.toLowerCase());
      const matchesCategory = category === 'todas' || course.category === category;
      return matchesTab && matchesQuery && matchesCategory;
    });
  }, [tab, query, category]);

  const pageCount = Math.max(1, Math.ceil(filtered.length / pageSize));
  const visible = filtered.slice((page - 1) * pageSize, page * pageSize);

  return (
    <>
      <PageHeader
        title="Cursos"
        description="Gerir catálogo, formadores e estados de publicação"
        actions={
        <>
            <Button size="sm" icon={DownloadIcon}>
              Exportar
            </Button>
            <Button size="sm" variant="primary" icon={PlusIcon}>
              Novo curso
            </Button>
          </>
        } />
      

      <Section bodyClassName="p-0">
        <div className="px-4 pt-3">
          <Tabs
            items={tabs}
            value={tab}
            onChange={(id) => {
              setTab(id);
              setPage(1);
            }} />
          
        </div>

        {/* Toolbar */}
        <div className="flex flex-wrap items-center gap-2 border-b border-line px-4 py-2.5">
          <div className="w-full sm:w-64">
            <Input
              icon={SearchIcon}
              placeholder="Pesquisar curso ou formador"
              aria-label="Pesquisar cursos"
              value={query}
              onChange={(event) => {
                setQuery(event.target.value);
                setPage(1);
              }}
              className="h-8 text-xs" />
            
          </div>
          <div className="w-36">
            <Select
              aria-label="Categoria"
              value={category}
              onChange={(event) => {
                setCategory(event.target.value);
                setPage(1);
              }}
              className="h-8 text-xs"
              options={[
              { value: 'todas', label: 'Todas as áreas' },
              { value: 'Escritório', label: 'Escritório' },
              { value: 'Negócios', label: 'Negócios' },
              { value: 'Finanças', label: 'Finanças' },
              { value: 'Tecnologia', label: 'Tecnologia' },
              { value: 'Idiomas', label: 'Idiomas' },
              { value: 'Indústria', label: 'Indústria' },
              { value: 'Criativo', label: 'Criativo' }]
              } />
            
          </div>
          <div className="w-32">
            <Select
              aria-label="Ordenar por"
              className="h-8 text-xs"
              options={[
              { value: 'recentes', label: 'Mais recentes' },
              { value: 'alunos', label: 'Mais alunos' },
              { value: 'receita', label: 'Maior receita' }]
              } />
            
          </div>
          <Button size="sm" icon={SlidersHorizontalIcon} className="ml-auto">
            Filtros
          </Button>
        </div>

        <Table>
          <THead>
            <TR hoverable={false}>
              <TH>Curso</TH>
              <TH>Formador</TH>
              <TH align="right">Alunos</TH>
              <TH align="right">Preço (MZN)</TH>
              <TH align="right">Avaliação</TH>
              <TH>Estado</TH>
              <TH align="right">Actualizado</TH>
              <TH align="right">Acções</TH>
            </TR>
          </THead>
          <TBody>
            {visible.map((course) =>
            <TR key={course.id}>
                <TD className="max-w-[260px]">
                  <p className="truncate font-medium">{course.title}</p>
                  <p className="truncate text-2xs text-fg-subtle">{course.category}</p>
                </TD>
                <TD className="text-fg-muted">{course.instructor}</TD>
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
                    <IconButton icon={EyeIcon} label={`Pré-visualizar ${course.title}`} />
                    <Dropdown
                    items={[
                    { id: 'edit', label: 'Editar curso', icon: PencilIcon },
                    { id: 'duplicate', label: 'Duplicar', icon: CopyIcon },
                    {
                      id: 'delete',
                      label: 'Eliminar',
                      icon: Trash2Icon,
                      tone: 'danger',
                      onSelect: () => setConfirmDelete(course.title)
                    }]
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
                <TD colSpan={8} className="py-10 text-center text-xs text-fg-muted">
                  Nenhum curso corresponde aos filtros aplicados.
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

      <Modal
        open={confirmDelete !== null}
        onClose={() => setConfirmDelete(null)}
        title="Eliminar curso"
        description="Esta acção não pode ser revertida."
        footer={
        <>
            <Button size="sm" onClick={() => setConfirmDelete(null)}>
              Cancelar
            </Button>
            <Button size="sm" variant="danger" onClick={() => setConfirmDelete(null)}>
              Eliminar
            </Button>
          </>
        }>
        
        O curso <span className="font-medium text-fg">{confirmDelete}</span> e todo o progresso associado serão
        removidos da plataforma.
      </Modal>
    </>);

}