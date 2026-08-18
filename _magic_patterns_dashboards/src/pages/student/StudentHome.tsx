import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import {
  ArrowRightIcon,
  AwardIcon,
  CompassIcon,
  DownloadIcon,
  PlayIcon,
  VideoIcon } from
'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { Button } from '../../components/ui/Button';
import { Card, Section } from '../../components/ui/Surface';
import { ProgressBar } from '../../components/ui/Bits';
import { Segmented } from '../../components/ui/Tabs';
import { Badge } from '../../components/ui/Badge';
import { enrolledCourses, liveClasses } from '../../data/courses';

const certificates = [
{ id: 'cert1', title: 'Excel Básico', issued: '02 Jul 2026', code: 'AC-2026-0481' },
{ id: 'cert2', title: 'Atendimento ao Cliente', issued: '18 Mai 2026', code: 'AC-2026-0312' }];


export function StudentHome() {
  const [view, setView] = useState('grelha');
  const current = enrolledCourses[0];
  const rest = enrolledCourses.slice(1);

  return (
    <>
      <PageHeader
        title="Olá, Délcio"
        description="Tem 4 cursos em andamento e 1 aula ao vivo hoje"
        actions={
        <Link to="/aluno/catalogo">
            <Button size="sm" icon={CompassIcon}>
              Explorar catálogo
            </Button>
          </Link>
        } />
      

      {/* Continue learning — the single most important thing on this screen */}
      <Card className="border-accent p-0" interactive>
        <div className="flex flex-col gap-4 p-4 md:flex-row md:items-center">
          <div className="min-w-0 flex-1">
            <Badge tone="accent">Continuar a aprender</Badge>
            <h2 className="mt-2 text-lg font-semibold tracking-tight text-fg">{current.title}</h2>
            <p className="mt-0.5 text-sm text-fg-muted">
              {current.nextLesson} · {current.duration} · {current.instructor}
            </p>
            <div className="mt-3 max-w-sm">
              <ProgressBar
                value={current.progress}
                label={`${current.modulesDone} de ${current.modulesTotal} módulos concluídos`} />
              
            </div>
          </div>
          <div className="flex shrink-0 items-center gap-2">
            <Button variant="primary" icon={PlayIcon}>
              Continuar
            </Button>
            <Button>Ver programa</Button>
          </div>
        </div>
      </Card>

      <div className="mt-3 grid grid-cols-1 gap-3 xl:grid-cols-3">
        <Section
          className="xl:col-span-2"
          title="Meus cursos"
          description="4 inscrições activas"
          action={
          <Segmented
            value={view}
            onChange={setView}
            items={[
            { id: 'grelha', label: 'Grelha' },
            { id: 'lista', label: 'Lista' }]
            } />

          }
          bodyClassName={view === 'grelha' ? 'grid grid-cols-1 gap-3 p-4 sm:grid-cols-2' : 'p-0'}>
          
          {view === 'grelha' ?
          rest.concat(current).map((course) =>
          <article key={course.id} className="rounded-md border border-line p-3">
                  <p className="truncate text-sm font-medium text-fg">{course.title}</p>
                  <p className="mt-0.5 truncate text-2xs text-fg-subtle">{course.instructor}</p>
                  <ProgressBar value={course.progress} className="mt-3" />
                  <div className="mt-3 flex items-center justify-between gap-2">
                    <p className="truncate text-2xs text-fg-muted">{course.nextLesson}</p>
                    <Button size="xs" variant="secondary" icon={PlayIcon}>
                      Continuar
                    </Button>
                  </div>
                </article>
          ) :

          <ul className="divide-y divide-line">
                  {rest.concat(current).map((course) =>
            <li key={course.id} className="flex items-center gap-3 px-4 py-2.5">
                      <div className="min-w-0 flex-1">
                        <p className="truncate text-sm font-medium text-fg">{course.title}</p>
                        <p className="truncate text-2xs text-fg-subtle">
                          {course.instructor} · {course.nextLesson}
                        </p>
                      </div>
                      <ProgressBar value={course.progress} className="w-20 shrink-0" />
                      <Button size="xs" variant="ghost" icon={PlayIcon}>
                        Continuar
                      </Button>
                    </li>
            )}
                </ul>
          }
        </Section>

        <div className="space-y-3">
          <Section title="Aulas ao vivo" bodyClassName="p-0">
            <ul className="divide-y divide-line">
              {liveClasses.slice(0, 2).map((item) =>
              <li key={item.id} className="px-4 py-2.5">
                  <div className="flex items-center justify-between gap-2">
                    <p className="truncate text-sm font-medium text-fg">{item.title}</p>
                    <Badge tone={item.date === 'Hoje' ? 'danger' : 'neutral'} dot>
                      {item.date}
                    </Badge>
                  </div>
                  <p className="mt-0.5 truncate text-2xs text-fg-subtle tnum">
                    {item.time} · {item.instructor}
                  </p>
                  {item.date === 'Hoje' ?
                <Button size="xs" variant="primary" icon={VideoIcon} className="mt-2">
                      Entrar na sala
                    </Button> :
                null}
                </li>
              )}
            </ul>
          </Section>

          <Section
            title="Certificados"
            description="2 emitidos"
            action={
            <Button variant="ghost" size="xs" iconRight={ArrowRightIcon} className="text-accent hover:text-accent">
                Ver todos
              </Button>
            }
            bodyClassName="p-0">
            
            <ul className="divide-y divide-line">
              {certificates.map((cert) =>
              <li key={cert.id} className="flex items-center gap-2.5 px-4 py-2.5">
                  <span className="flex h-7 w-7 shrink-0 items-center justify-center rounded-md border border-line bg-surface-2">
                    <AwardIcon className="h-3.5 w-3.5 text-fg-subtle" />
                  </span>
                  <div className="min-w-0 flex-1">
                    <p className="truncate text-xs font-medium text-fg">{cert.title}</p>
                    <p className="truncate text-2xs text-fg-subtle tnum">
                      {cert.issued} · {cert.code}
                    </p>
                  </div>
                  <Button size="xs" variant="ghost" icon={DownloadIcon}>
                    PDF
                  </Button>
                </li>
              )}
            </ul>
            <div className="border-t border-line px-4 py-2.5">
              <p className="text-2xs text-fg-muted">
                Conclua <span className="font-medium text-fg">Inglês Técnico</span> (91%) para receber o próximo
                certificado.
              </p>
            </div>
          </Section>
        </div>
      </div>
    </>);

}