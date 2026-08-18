import React from 'react';
import { CalendarIcon, PlayIcon, VideoIcon } from 'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { Button } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { Badge } from '../../components/ui/Badge';
import { liveClasses } from '../../data/courses';

const recordings = [
{ id: 'r1', title: 'Introdução às fórmulas', course: 'Excel Avançado', date: '08 Ago', duration: '58 min' },
{ id: 'r2', title: 'Análise de concorrência', course: 'Marketing Digital', date: '05 Ago', duration: '46 min' },
{ id: 'r3', title: 'Gráficos dinâmicos', course: 'Excel Avançado', date: '01 Ago', duration: '52 min' }];


export function StudentLive() {
  return (
    <>
      <PageHeader
        title="Aulas ao vivo"
        actions={
        <Button size="sm" icon={CalendarIcon}>
            Adicionar ao calendário
          </Button>
        } />
      

      <div className="grid grid-cols-1 gap-3 xl:grid-cols-3">
        <Section className="xl:col-span-2" title="Próximas" bodyClassName="p-0">
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
                    {item.course} · {item.time} · {item.instructor}
                  </p>
                </div>
                <Button
                size="xs"
                variant={item.date === 'Hoje' ? 'primary' : 'secondary'}
                icon={VideoIcon}
                disabled={item.date !== 'Hoje'}>
                
                  {item.date === 'Hoje' ? 'Entrar' : 'Agendada'}
                </Button>
              </li>
            )}
          </ul>
        </Section>

        <Section title="Gravações" bodyClassName="p-0">
          <ul className="divide-y divide-line">
            {recordings.map((item) =>
            <li key={item.id} className="flex items-center gap-2.5 px-4 py-2.5">
                <div className="min-w-0 flex-1">
                  <p className="truncate text-xs font-medium text-fg">{item.title}</p>
                  <p className="truncate text-2xs text-fg-subtle tnum">
                    {item.course} · {item.date} · {item.duration}
                  </p>
                </div>
                <Button size="xs" variant="ghost" icon={PlayIcon}>
                  Ver
                </Button>
              </li>
            )}
          </ul>
        </Section>
      </div>
    </>);

}