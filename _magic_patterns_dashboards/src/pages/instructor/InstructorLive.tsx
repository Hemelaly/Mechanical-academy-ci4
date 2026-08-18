import React, { useState } from 'react';
import { CalendarPlusIcon, CopyIcon, MoreHorizontalIcon, VideoIcon } from 'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { Button, IconButton } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { Badge } from '../../components/ui/Badge';
import { Table, TBody, TD, TH, THead, TR } from '../../components/ui/Table';
import { Dropdown } from '../../components/ui/Dropdown';
import { Modal } from '../../components/ui/Feedback';
import { FieldGroup, Input, Select } from '../../components/ui/Field';
import { liveClasses } from '../../data/courses';

const past = [
{ id: 'p1', title: 'Introdução às fórmulas', course: 'Excel Avançado', date: '08 Ago', attendance: 62, total: 84 },
{ id: 'p2', title: 'Análise de concorrência', course: 'Marketing Digital', date: '05 Ago', attendance: 41, total: 58 },
{ id: 'p3', title: 'Gráficos dinâmicos', course: 'Excel Avançado', date: '01 Ago', attendance: 70, total: 84 }];


export function InstructorLive() {
  const [open, setOpen] = useState(false);

  return (
    <>
      <PageHeader
        title="Aulas ao vivo"
        actions={
        <Button size="sm" variant="primary" icon={CalendarPlusIcon} onClick={() => setOpen(true)}>
            Agendar aula
          </Button>
        } />
      

      <div className="grid grid-cols-1 gap-3 xl:grid-cols-3">
        <Section className="xl:col-span-2" title="Agendadas" bodyClassName="p-0">
          <ul className="divide-y divide-line">
            {liveClasses.map((item) =>
            <li key={item.id} className="flex flex-wrap items-center gap-3 px-4 py-3">
                <div className="min-w-0 flex-1">
                  <div className="flex items-center gap-2">
                    <p className="truncate text-sm font-medium text-fg">{item.title}</p>
                    <Badge tone={item.date === 'Hoje' ? 'danger' : 'neutral'} dot>
                      {item.date}
                    </Badge>
                  </div>
                  <p className="mt-0.5 truncate text-2xs text-fg-subtle tnum">
                    {item.course} · {item.time} · 60 min
                  </p>
                </div>
                <div className="flex items-center gap-1.5">
                  <IconButton icon={CopyIcon} label="Copiar link" />
                  <Button size="xs" variant={item.date === 'Hoje' ? 'primary' : 'secondary'} icon={VideoIcon}>
                    {item.date === 'Hoje' ? 'Iniciar' : 'Sala'}
                  </Button>
                  <Dropdown
                  items={[
                  { id: 'editar', label: 'Editar' },
                  { id: 'notificar', label: 'Notificar alunos' },
                  { id: 'cancelar', label: 'Cancelar', tone: 'danger' }]
                  }
                  trigger={({ toggle }) =>
                  <IconButton icon={MoreHorizontalIcon} label="Mais acções" onClick={toggle} />
                  } />
                
                </div>
              </li>
            )}
          </ul>
        </Section>

        <Section title="Realizadas" bodyClassName="p-0">
          <Table>
            <THead>
              <TR hoverable={false}>
                <TH>Sessão</TH>
                <TH align="right">Presenças</TH>
                <TH align="right">Data</TH>
              </TR>
            </THead>
            <TBody>
              {past.map((item) =>
              <TR key={item.id}>
                  <TD className="max-w-[160px]">
                    <p className="truncate font-medium">{item.title}</p>
                    <p className="truncate text-2xs text-fg-subtle">{item.course}</p>
                  </TD>
                  <TD align="right" className="tnum text-fg-muted">
                    {item.attendance}/{item.total}
                  </TD>
                  <TD align="right" className="text-2xs text-fg-subtle">
                    {item.date}
                  </TD>
                </TR>
              )}
            </TBody>
          </Table>
        </Section>
      </div>

      <Modal
        open={open}
        onClose={() => setOpen(false)}
        title="Agendar aula ao vivo"
        footer={
        <>
            <Button size="sm" onClick={() => setOpen(false)}>
              Cancelar
            </Button>
            <Button size="sm" variant="primary" onClick={() => setOpen(false)}>
              Agendar
            </Button>
          </>
        }>
        
        <div className="space-y-3">
          <FieldGroup label="Título" required htmlFor="live-titulo">
            <Input id="live-titulo" placeholder="Ex.: Sessão de dúvidas" />
          </FieldGroup>
          <FieldGroup label="Curso" required htmlFor="live-curso">
            <Select
              id="live-curso"
              options={[
              { value: 'excel', label: 'Excel Avançado para Gestão' },
              { value: 'marketing', label: 'Marketing Digital para PMEs' }]
              } />
            
          </FieldGroup>
          <div className="grid grid-cols-2 gap-3">
            <FieldGroup label="Data" required htmlFor="live-data">
              <Input id="live-data" type="date" defaultValue="2026-08-15" />
            </FieldGroup>
            <FieldGroup label="Hora" required htmlFor="live-hora">
              <Input id="live-hora" type="time" defaultValue="18:00" />
            </FieldGroup>
          </div>
        </div>
      </Modal>
    </>);

}