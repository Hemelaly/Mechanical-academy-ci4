import React from 'react';
import { Link } from 'react-router-dom';
import { BookOpenIcon, CompassIcon } from 'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { Button } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { EmptyState } from '../../components/ui/Feedback';
import { Tabs } from '../../components/ui/Tabs';

export function StudentCourses() {
  return (
    <>
      <PageHeader title="Meus cursos" description="Cursos em que está inscrito" />
      <Section bodyClassName="p-0">
        <div className="px-4 pt-3">
          <Tabs
            items={[
            { id: 'todos', label: 'Todos', count: 0 },
            { id: 'andamento', label: 'Em andamento', count: 0 },
            { id: 'concluidos', label: 'Concluídos', count: 0 }]
            }
            value="todos"
            onChange={() => undefined} />
          
        </div>
        <EmptyState
          icon={BookOpenIcon}
          title="Ainda não tem cursos"
          description="Quando se inscrever num curso, ele aparece aqui com o seu progresso, aulas e certificados."
          action={
          <>
              <Link to="/aluno/catalogo">
                <Button variant="primary" size="sm" icon={CompassIcon}>
                  Explorar catálogo
                </Button>
              </Link>
              <Button size="sm">Usar código de convite</Button>
            </>
          } />
        
      </Section>
    </>);

}