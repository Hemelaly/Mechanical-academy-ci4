import React, { useMemo, useState } from 'react';
import { CheckIcon, ClockIcon, CompassIcon, PlayIcon, StarIcon } from 'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { Toolbar } from '../../components/dashboard/Toolbar';
import { Button } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { Badge } from '../../components/ui/Badge';
import { EmptyState } from '../../components/ui/Feedback';
import { catalogCourses } from '../../data/catalog';
import { formatNumber } from '../../utils/format';

export function StudentCatalog() {
  const [query, setQuery] = useState('');
  const [area, setArea] = useState('todas');
  const [level, setLevel] = useState('todos');

  const filtered = useMemo(
    () =>
    catalogCourses.filter((course) => {
      const matchesQuery =
      query.trim() === '' ||
      course.title.toLowerCase().includes(query.toLowerCase()) ||
      course.instructor.toLowerCase().includes(query.toLowerCase());
      const matchesArea = area === 'todas' || course.category === area;
      const matchesLevel = level === 'todos' || course.level === level;
      return matchesQuery && matchesArea && matchesLevel;
    }),
    [query, area, level]
  );

  return (
    <>
      <PageHeader title="Catálogo" />

      <Section bodyClassName="p-0">
        <Toolbar
          placeholder="Pesquisar curso ou formador"
          query={query}
          onQueryChange={setQuery}
          filters={[
          {
            id: 'area',
            ariaLabel: 'Área',
            width: 'w-40',
            value: area,
            onChange: setArea,
            options: [
            { value: 'todas', label: 'Todas as áreas' },
            { value: 'Escritório', label: 'Escritório' },
            { value: 'Negócios', label: 'Negócios' },
            { value: 'Finanças', label: 'Finanças' },
            { value: 'Tecnologia', label: 'Tecnologia' },
            { value: 'Idiomas', label: 'Idiomas' },
            { value: 'Indústria', label: 'Indústria' },
            { value: 'Criativo', label: 'Criativo' }]

          },
          {
            id: 'nivel',
            ariaLabel: 'Nível',
            value: level,
            onChange: setLevel,
            options: [
            { value: 'todos', label: 'Todos os níveis' },
            { value: 'Iniciante', label: 'Iniciante' },
            { value: 'Intermédio', label: 'Intermédio' },
            { value: 'Avançado', label: 'Avançado' }]

          }]
          } />
        

        {filtered.length === 0 ?
        <EmptyState
          icon={CompassIcon}
          title="Nenhum curso encontrado"
          description="Ajuste a pesquisa ou os filtros." /> :


        <ul className="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 xl:grid-cols-3">
            {filtered.map((course) =>
          <li key={course.id} className="flex flex-col rounded-md border border-line p-3">
                <div className="flex items-start justify-between gap-2">
                  <Badge tone="neutral">{course.category}</Badge>
                  {course.rating > 0 ?
              <span className="inline-flex items-center gap-1 text-2xs text-fg-muted tnum">
                      <StarIcon className="h-3 w-3 text-warn" />
                      {course.rating.toFixed(1).replace('.', ',')} · {formatNumber(course.students)}
                    </span> :

              <span className="text-2xs text-fg-subtle">Novo</span>
              }
                </div>

                <h3 className="mt-2 text-sm font-medium leading-5 text-fg">{course.title}</h3>
                <p className="mt-0.5 text-2xs text-fg-subtle">{course.instructor}</p>

                <p className="mt-2 flex items-center gap-2 text-2xs text-fg-muted tnum">
                  <ClockIcon className="h-3 w-3" />
                  {course.duration} · {course.lessons} aulas · {course.level}
                </p>

                <div className="mt-auto flex items-center justify-between gap-2 pt-3">
                  <span className="text-sm font-semibold text-fg tnum">{formatNumber(course.price)} MZN</span>
                  {course.enrolled ?
              <Button size="xs" icon={PlayIcon}>
                      Continuar
                    </Button> :

              <Button size="xs" variant="primary" icon={CheckIcon}>
                      Inscrever
                    </Button>
              }
                </div>
              </li>
          )}
          </ul>
        }
      </Section>
    </>);

}