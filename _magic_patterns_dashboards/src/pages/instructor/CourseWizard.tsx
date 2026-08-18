import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { ArrowLeftIcon, ArrowRightIcon, GripVerticalIcon, PlusIcon, SaveIcon, XIcon } from 'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { Button, IconButton } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { Checkbox, FieldGroup, Input, Select, Textarea } from '../../components/ui/Field';
import { Stepper, type Step } from '../../components/ui/Stepper';
import { UploadZone } from '../../components/ui/Bits';
import { Badge } from '../../components/ui/Badge';
import { Toast } from '../../components/ui/Feedback';

const steps: Step[] = [
{ id: 'basico', label: 'Informação', hint: 'Título e área' },
{ id: 'conteudo', label: 'Conteúdo', hint: 'Módulos e aulas' },
{ id: 'preco', label: 'Preço', hint: 'MZN e acesso' },
{ id: 'revisao', label: 'Revisão', hint: 'Publicar' }];


const modules = [
{ id: 'm1', title: 'Módulo 1 — Fundamentos', lessons: 4, duration: '48 min' },
{ id: 'm2', title: 'Módulo 2 — Fórmulas essenciais', lessons: 6, duration: '1 h 12 min' },
{ id: 'm3', title: 'Módulo 3 — Tabelas dinâmicas', lessons: 5, duration: '58 min' }];


export function CourseWizard() {
  const [current, setCurrent] = useState(0);
  const [saved, setSaved] = useState(false);
  const navigate = useNavigate();
  const last = current === steps.length - 1;

  return (
    <div className="-mb-5 lg:-mb-6">
      <PageHeader
        title="Novo curso"
        description="Rascunho · guardado automaticamente"
        actions={
        <Badge tone="neutral" dot>
            Rascunho
          </Badge>
        } />
      

      <Stepper steps={steps} current={current} onStepChange={setCurrent} className="mb-3" />

      {current === 0 ?
      <Section title="Informação do curso" description="Como o curso aparece no catálogo">
          <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
            <FieldGroup label="Título do curso" required className="md:col-span-2" htmlFor="titulo">
              <Input id="titulo" defaultValue="Excel Avançado para Gestão" />
            </FieldGroup>
            <FieldGroup label="Área" required htmlFor="area">
              <Select
              id="area"
              options={[
              { value: 'escritorio', label: 'Escritório' },
              { value: 'negocios', label: 'Negócios' },
              { value: 'tecnologia', label: 'Tecnologia' },
              { value: 'idiomas', label: 'Idiomas' }]
              } />
            
            </FieldGroup>
            <FieldGroup label="Nível" htmlFor="nivel">
              <Select
              id="nivel"
              options={[
              { value: 'iniciante', label: 'Iniciante' },
              { value: 'intermedio', label: 'Intermédio' },
              { value: 'avancado', label: 'Avançado' }]
              } />
            
            </FieldGroup>
            <FieldGroup
            label="Descrição curta"
            hint="Máx. 180 caracteres"
            className="md:col-span-2"
            htmlFor="descricao">
            
              <Textarea
              id="descricao"
              rows={3}
              defaultValue="Domine tabelas dinâmicas, fórmulas avançadas e dashboards para relatórios de gestão." />
            
            </FieldGroup>
            <FieldGroup label="Imagem de capa" className="md:col-span-2">
              <UploadZone title="Carregar imagem de capa" hint="JPG ou PNG, 1280×720 recomendado" />
            </FieldGroup>
          </div>
        </Section> :
      null}

      {current === 1 ?
      <div className="space-y-3">
          <Section
          title="Estrutura do curso"
          description="3 módulos · 15 aulas · 2 h 58 min"
          action={
          <Button size="xs" icon={PlusIcon}>
                Módulo
              </Button>
          }
          bodyClassName="p-0">
          
            <ul className="divide-y divide-line">
              {modules.map((module) =>
            <li key={module.id} className="flex items-center gap-3 px-4 py-2.5">
                  <GripVerticalIcon className="h-4 w-4 shrink-0 cursor-grab text-fg-subtle" />
                  <div className="min-w-0 flex-1">
                    <p className="truncate text-sm font-medium text-fg">{module.title}</p>
                    <p className="truncate text-2xs text-fg-subtle tnum">
                      {module.lessons} aulas · {module.duration}
                    </p>
                  </div>
                  <Button size="xs" variant="ghost">
                    Editar
                  </Button>
                  <IconButton icon={XIcon} label={`Remover ${module.title}`} />
                </li>
            )}
            </ul>
          </Section>
          <Section title="Materiais de apoio">
            <UploadZone />
          </Section>
        </div> :
      null}

      {current === 2 ?
      <Section title="Preço e acesso" description="Valores em meticais (MZN)">
          <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
            <FieldGroup label="Preço" required hint="MZN" htmlFor="preco">
              <Input id="preco" type="number" defaultValue={2500} className="tnum" />
            </FieldGroup>
            <FieldGroup label="Preço promocional" hint="Opcional" htmlFor="promo">
              <Input id="promo" type="number" placeholder="0" className="tnum" />
            </FieldGroup>
            <FieldGroup label="Métodos de pagamento" className="md:col-span-2">
              <div className="grid grid-cols-2 gap-2 rounded-md border border-line p-3 sm:grid-cols-4">
                <Checkbox label="M-Pesa" defaultChecked />
                <Checkbox label="e-Mola" defaultChecked />
                <Checkbox label="Transferência" />
                <Checkbox label="Cartão" />
              </div>
            </FieldGroup>
            <FieldGroup label="Duração do acesso" htmlFor="acesso">
              <Select
              id="acesso"
              options={[
              { value: 'vitalicio', label: 'Vitalício' },
              { value: '12m', label: '12 meses' },
              { value: '6m', label: '6 meses' }]
              } />
            
            </FieldGroup>
            <FieldGroup label="Certificado" htmlFor="certificado">
              <Select
              id="certificado"
              options={[
              { value: 'automatico', label: 'Emitir automaticamente a 100%' },
              { value: 'manual', label: 'Emitir manualmente' },
              { value: 'nenhum', label: 'Sem certificado' }]
              } />
            
            </FieldGroup>
          </div>
        </Section> :
      null}

      {current === 3 ?
      <div className="grid grid-cols-1 gap-3 lg:grid-cols-3">
          <Section className="lg:col-span-2" title="Revisão final" bodyClassName="p-0">
            <dl className="divide-y divide-line text-sm">
              {[
            ['Título', 'Excel Avançado para Gestão'],
            ['Área / Nível', 'Escritório · Avançado'],
            ['Conteúdo', '3 módulos · 15 aulas · 2 h 58 min'],
            ['Preço', '2 500 MZN · M-Pesa, e-Mola'],
            ['Certificado', 'Automático a 100%']].
            map(([label, value]) =>
            <div key={label} className="flex items-center justify-between gap-3 px-4 py-2.5">
                  <dt className="text-xs text-fg-muted">{label}</dt>
                  <dd className="truncate text-sm font-medium text-fg">{value}</dd>
                </div>
            )}
            </dl>
          </Section>
          <Section title="Antes de publicar">
            <ul className="space-y-2">
              <Checkbox label="Conteúdo revisto" description="Todas as aulas têm vídeo e descrição." defaultChecked />
              <Checkbox label="Preço confirmado" description="Comissão da plataforma: 30%." defaultChecked />
              <Checkbox label="Notificar alunos" description="Enviar email aos 744 alunos." />
            </ul>
          </Section>
        </div> :
      null}

      {saved ?
      <div className="pointer-events-none fixed bottom-20 right-5 z-40">
          <div className="pointer-events-auto">
            <Toast
            tone="success"
            title="Rascunho guardado"
            description="As alterações foram guardadas às 09:48."
            onClose={() => setSaved(false)} />
          
          </div>
        </div> :
      null}

      {/* Sticky wizard footer — sticks to the bottom of the scrolling content area */}
      <div className="sticky bottom-0 z-20 -mx-4 mt-3 border-t border-line bg-surface lg:-mx-6">
        <div className="flex items-center justify-between gap-3 px-4 py-2.5 lg:px-6">
          <p className="hidden text-xs text-fg-muted sm:block tnum">
            Passo {current + 1} de {steps.length} · {steps[current].label}
          </p>
          <div className="flex flex-1 items-center justify-end gap-2">
            <Button
              size="sm"
              icon={ArrowLeftIcon}
              disabled={current === 0}
              onClick={() => setCurrent((v) => Math.max(0, v - 1))}>
              
              Voltar
            </Button>
            <Button
              size="sm"
              icon={SaveIcon}
              onClick={() => {
                setSaved(true);
                window.setTimeout(() => setSaved(false), 3200);
              }}>
              
              Guardar
            </Button>
            <Button
              size="sm"
              variant="primary"
              iconRight={last ? undefined : ArrowRightIcon}
              onClick={() => last ? navigate('/instrutor') : setCurrent((v) => Math.min(steps.length - 1, v + 1))}>
              
              {last ? 'Publicar curso' : 'Continuar'}
            </Button>
          </div>
        </div>
      </div>
    </div>);

}