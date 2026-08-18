import React, { useMemo, useState } from 'react';
import { CheckIcon, MoreHorizontalIcon, StarIcon, UserRoundPlusIcon } from 'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { Toolbar } from '../../components/dashboard/Toolbar';
import { PersonStatusBadge } from '../../components/dashboard/status';
import { Button, IconButton } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { Avatar } from '../../components/ui/Bits';
import { Table, TBody, TD, TH, THead, TR } from '../../components/ui/Table';
import { Pagination } from '../../components/ui/Pagination';
import { Dropdown } from '../../components/ui/Dropdown';
import { Modal } from '../../components/ui/Feedback';
import { FieldGroup, Input, Select } from '../../components/ui/Field';
import { instructors } from '../../data/people';
import { formatMZN, formatNumber } from '../../utils/format';

export function AdminInstructors() {
  const [query, setQuery] = useState('');
  const [inviteOpen, setInviteOpen] = useState(false);
  const [page, setPage] = useState(1);
  const pageSize = 6;

  const filtered = useMemo(
    () =>
    instructors.filter(
      (item) =>
      query.trim() === '' ||
      item.name.toLowerCase().includes(query.toLowerCase()) ||
      item.email.toLowerCase().includes(query.toLowerCase())
    ),
    [query]
  );

  const pageCount = Math.max(1, Math.ceil(filtered.length / pageSize));
  const visible = filtered.slice((page - 1) * pageSize, page * pageSize);

  return (
    <>
      <PageHeader
        title="Formadores"
        actions={
        <Button size="sm" variant="primary" icon={UserRoundPlusIcon} onClick={() => setInviteOpen(true)}>
            Convidar formador
          </Button>
        } />
      

      <Section bodyClassName="p-0">
        <Toolbar
          placeholder="Pesquisar formador"
          query={query}
          onQueryChange={(value) => {
            setQuery(value);
            setPage(1);
          }}
          filters={[
          {
            id: 'estado',
            ariaLabel: 'Estado',
            options: [
            { value: 'todos', label: 'Todos os estados' },
            { value: 'activo', label: 'Activos' },
            { value: 'pendente', label: 'Pendentes' },
            { value: 'inactivo', label: 'Inactivos' }]

          }]
          } />
        

        <Table>
          <THead>
            <TR hoverable={false}>
              <TH>Formador</TH>
              <TH align="right">Cursos</TH>
              <TH align="right">Alunos</TH>
              <TH align="right">Receita gerada</TH>
              <TH align="right">Avaliação</TH>
              <TH>Estado</TH>
              <TH align="right">Acções</TH>
            </TR>
          </THead>
          <TBody>
            {visible.map((item) =>
            <TR key={item.id}>
                <TD>
                  <div className="flex items-center gap-2.5">
                    <Avatar name={item.name} size="sm" />
                    <div className="min-w-0">
                      <p className="truncate font-medium">{item.name}</p>
                      <p className="truncate text-2xs text-fg-subtle">{item.email}</p>
                    </div>
                  </div>
                </TD>
                <TD align="right" className="tnum text-fg-muted">
                  {item.courses}
                </TD>
                <TD align="right" className="tnum text-fg-muted">
                  {formatNumber(item.students)}
                </TD>
                <TD align="right" className="tnum font-medium">
                  {formatMZN(item.revenue, { compact: true })}
                </TD>
                <TD align="right">
                  {item.rating > 0 ?
                <span className="inline-flex items-center gap-1 text-fg-muted tnum">
                      <StarIcon className="h-3 w-3 text-warn" />
                      {item.rating.toFixed(1).replace('.', ',')}
                    </span> :

                <span className="text-fg-subtle">—</span>
                }
                </TD>
                <TD>
                  <PersonStatusBadge status={item.status} />
                </TD>
                <TD align="right">
                  <div className="flex items-center justify-end gap-0.5">
                    {item.status === 'pendente' ?
                  <Button size="xs" variant="primary" icon={CheckIcon}>
                        Aprovar
                      </Button> :
                  null}
                    <Dropdown
                    items={[
                    { id: 'ver', label: 'Ver perfil' },
                    { id: 'comissao', label: 'Editar comissão' },
                    { id: 'desactivar', label: 'Desactivar', tone: 'danger' }]
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
        open={inviteOpen}
        onClose={() => setInviteOpen(false)}
        title="Convidar formador"
        footer={
        <>
            <Button size="sm" onClick={() => setInviteOpen(false)}>
              Cancelar
            </Button>
            <Button size="sm" variant="primary" onClick={() => setInviteOpen(false)}>
              Enviar convite
            </Button>
          </>
        }>
        
        <div className="space-y-3">
          <FieldGroup label="Nome" required htmlFor="inv-nome">
            <Input id="inv-nome" placeholder="Nome completo" />
          </FieldGroup>
          <FieldGroup label="Email" required htmlFor="inv-email">
            <Input id="inv-email" type="email" placeholder="nome@academy.co.mz" />
          </FieldGroup>
          <FieldGroup label="Comissão" hint="%" htmlFor="inv-comissao">
            <Select
              id="inv-comissao"
              options={[
              { value: '70', label: '70% para o formador' },
              { value: '60', label: '60% para o formador' },
              { value: '50', label: '50% para o formador' }]
              } />
            
          </FieldGroup>
        </div>
      </Modal>
    </>);

}