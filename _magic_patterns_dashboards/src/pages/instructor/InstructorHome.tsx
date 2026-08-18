import React from 'react';
import { Link } from 'react-router-dom';
import {
  ArrowRightIcon,
  BookOpenIcon,
  PlusIcon,
  StarIcon,
  UsersIcon,
  VideoIcon,
  WalletIcon } from
'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { ActivityFeed } from '../../components/dashboard/ActivityFeed';
import { EarningsChart } from '../../components/dashboard/Charts';
import { CourseStatusBadge } from '../../components/dashboard/status';
import { Button } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { Avatar, ProgressBar, StatCard } from '../../components/ui/Bits';
import { Table, TBody, TD, TH, THead, TR } from '../../components/ui/Table';
import { instructorActivity } from '../../data/activity';
import { courses, liveClasses } from '../../data/courses';
import { formatMZN, formatNumber } from '../../utils/format';

const myCourses = courses.filter((course) => course.instructor === 'Ana Chirindza' || course.id === 'c6');

const topStudents = [
{ name: 'Célia Bila', course: 'Excel Avançado', progress: 92 },
{ name: 'Jorge Cuna', course: 'Marketing Digital', progress: 78 },
{ name: 'Délcio Nhaca', course: 'Marketing Digital', progress: 64 },
{ name: 'Nádia Ferrão', course: 'Excel Avançado', progress: 51 }];


export function InstructorHome() {
  return (
    <>
      <PageHeader
        title="Painel do formador"
        description="Bem-vinda de volta, Ana"
        actions={
        <>
            <Button size="sm" icon={VideoIcon}>
              Agendar aula
            </Button>
            <Link to="/instrutor/cursos/novo">
              <Button size="sm" variant="primary" icon={PlusIcon}>
                Criar curso
              </Button>
            </Link>
          </>
        } />
      

      <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <StatCard
          label="Ganhos do mês"
          value={formatMZN(71900)}
          delta={13.4}
          deltaLabel="M-Pesa · 15 Ago"
          icon={WalletIcon} />
        
        <StatCard label="Alunos activos" value={formatNumber(744)} delta={5.2} deltaLabel="+38 esta semana" icon={UsersIcon} />
        <StatCard label="Cursos" value="3" deltaLabel="1 rascunho" delta={0} icon={BookOpenIcon} />
        <StatCard label="Avaliação média" value="4,7" delta={1.1} deltaLabel="212 avaliações" icon={StarIcon} />
      </div>

      <div className="mt-3 grid grid-cols-1 gap-3 xl:grid-cols-3">
        <Section
          className="xl:col-span-2"
          title="Ganhos"
          description="Últimos 6 meses (MZN, líquido de comissão)"
          action={
          <Button variant="ghost" size="xs" iconRight={ArrowRightIcon} className="text-accent hover:text-accent">
              Ver ganhos
            </Button>
          }
          bodyClassName="px-2 py-3">
          
          <EarningsChart />
        </Section>

        <Section title="Próximas aulas ao vivo" bodyClassName="p-0">
          <ul className="divide-y divide-line">
            {liveClasses.map((item) =>
            <li key={item.id} className="flex items-center gap-3 px-4 py-2.5">
                <div className="min-w-0 flex-1">
                  <p className="truncate text-sm font-medium text-fg">{item.title}</p>
                  <p className="truncate text-2xs text-fg-subtle">{item.course}</p>
                </div>
                <div className="shrink-0 text-right">
                  <p className="text-xs font-medium text-fg">{item.date}</p>
                  <p className="text-2xs text-fg-subtle tnum">{item.time}</p>
                </div>
              </li>
            )}
          </ul>
          <div className="border-t border-line p-2">
            <Button size="sm" block icon={VideoIcon}>
              Nova sessão
            </Button>
          </div>
        </Section>
      </div>

      <div className="mt-3 grid grid-cols-1 gap-3 xl:grid-cols-3">
        <Section className="xl:col-span-2" title="Meus cursos" bodyClassName="p-0">
          <Table>
            <THead>
              <TR hoverable={false}>
                <TH>Curso</TH>
                <TH align="right">Alunos</TH>
                <TH align="right">Preço</TH>
                <TH>Estado</TH>
                <TH align="right">Actualizado</TH>
              </TR>
            </THead>
            <TBody>
              {myCourses.map((course) =>
              <TR key={course.id}>
                  <TD className="max-w-[240px]">
                    <p className="truncate font-medium">{course.title}</p>
                    <p className="truncate text-2xs text-fg-subtle">{course.category}</p>
                  </TD>
                  <TD align="right" className="tnum text-fg-muted">
                    {formatNumber(course.students)}
                  </TD>
                  <TD align="right" className="tnum text-fg-muted">
                    {formatNumber(course.price)}
                  </TD>
                  <TD>
                    <CourseStatusBadge status={course.status} />
                  </TD>
                  <TD align="right" className="text-2xs text-fg-subtle">
                    {course.updated}
                  </TD>
                </TR>
              )}
            </TBody>
          </Table>
        </Section>

        <div className="space-y-3">
          <Section title="Alunos em destaque" bodyClassName="p-0">
            <ul className="divide-y divide-line">
              {topStudents.map((student) =>
              <li key={student.name} className="flex items-center gap-2.5 px-4 py-2.5">
                  <Avatar name={student.name} size="sm" />
                  <div className="min-w-0 flex-1">
                    <p className="truncate text-xs font-medium text-fg">{student.name}</p>
                    <p className="truncate text-2xs text-fg-subtle">{student.course}</p>
                  </div>
                  <ProgressBar value={student.progress} className="w-16 shrink-0" />
                </li>
              )}
            </ul>
          </Section>

          <Section title="Actividade" bodyClassName="p-0">
            <ActivityFeed items={instructorActivity} />
          </Section>
        </div>
      </div>
    </>);

}