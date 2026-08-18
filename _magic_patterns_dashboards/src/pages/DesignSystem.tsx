import React, { useState } from 'react';
import { BookOpenIcon, PlusIcon, SearchIcon, Trash2Icon } from 'lucide-react';
import { PageHeader } from '../components/shell/PageHeader';
import { Button, IconButton } from '../components/ui/Button';
import { Badge } from '../components/ui/Badge';
import { Card, Section } from '../components/ui/Surface';
import { Checkbox, FieldGroup, Input, Select, Textarea } from '../components/ui/Field';
import { Table, TBody, TD, TH, THead, TR } from '../components/ui/Table';
import { Segmented, Tabs } from '../components/ui/Tabs';
import { EmptyState, Modal, Skeleton, Toast } from '../components/ui/Feedback';
import { Pagination } from '../components/ui/Pagination';
import { Stepper } from '../components/ui/Stepper';
import { Dropdown } from '../components/ui/Dropdown';
import { Avatar, ProgressBar, StatCard, StatCardSkeleton, UploadZone } from '../components/ui/Bits';
import { useTheme } from '../contexts/ThemeContext';

const colorGroups: {title: string;tokens: {name: string;variable: string;note?: string;}[];}[] = [
{
  title: 'Superfícies e neutros',
  tokens: [
  { name: 'canvas', variable: '--canvas', note: 'Fundo da app' },
  { name: 'surface', variable: '--surface', note: 'Painéis' },
  { name: 'surface-2', variable: '--surface-2', note: 'Cabeçalhos de tabela' },
  { name: 'surface-3', variable: '--surface-3', note: 'Trilhos, hover' },
  { name: 'line', variable: '--line', note: 'Bordas hairline' },
  { name: 'line-strong', variable: '--line-strong' }]

},
{
  title: 'Texto',
  tokens: [
  { name: 'fg', variable: '--fg' },
  { name: 'fg-muted', variable: '--fg-muted' },
  { name: 'fg-subtle', variable: '--fg-subtle', note: 'Meta / caption' }]

},
{
  title: 'Acento (único)',
  tokens: [
  { name: 'accent', variable: '--accent', note: 'CTA, nav activa, foco' },
  { name: 'accent-hover', variable: '--accent-hover' },
  { name: 'accent-active', variable: '--accent-active' },
  { name: 'accent-soft', variable: '--accent-soft' }]

},
{
  title: 'Semântico',
  tokens: [
  { name: 'success', variable: '--success' },
  { name: 'warn', variable: '--warn' },
  { name: 'danger', variable: '--danger' },
  { name: 'info', variable: '--info' }]

}];


const typeScale = [
{ label: 'Título de página', className: 'text-xl font-semibold tracking-tight', spec: '18/25 · 600' },
{ label: 'Título de secção', className: 'text-sm font-semibold', spec: '13/19 · 600' },
{ label: 'Corpo', className: 'text-sm', spec: '13/19 · 400' },
{ label: 'Corpo compacto', className: 'text-xs', spec: '11.5/16 · 400' },
{ label: 'Meta / etiqueta', className: 'text-2xs uppercase tracking-widest text-fg-subtle', spec: '10/14 · 600' },
{ label: 'Valor KPI', className: 'text-2xl font-semibold tnum', spec: '22/28 · 600 · tabular' }];


const spacing = [
{ token: '1.5', px: '6px', use: 'Gaps de ícone' },
{ token: '2', px: '8px', use: 'Padding de badge/botão xs' },
{ token: '2.5', px: '10px', use: 'Células de tabela' },
{ token: '3', px: '12px', use: 'Gap de grelha' },
{ token: '3.5', px: '14px', use: 'Padding de card' },
{ token: '4', px: '16px', use: 'Padding de secção' },
{ token: '5', px: '20px', use: 'Padding da área de conteúdo' }];


export function DesignSystem() {
  const { theme, toggleTheme } = useTheme();
  const [tab, setTab] = useState('cores');
  const [segment, setSegment] = useState('30d');
  const [step, setStep] = useState(1);
  const [modalOpen, setModalOpen] = useState(false);
  const [page, setPage] = useState(2);

  return (
    <>
      <PageHeader
        title="Design system"
        description="Tokens, densidade e componentes partilhados pelos três painéis"
        actions={
        <>
            <Badge tone="accent">raio = rounded-md (6px)</Badge>
            <Button size="sm" onClick={toggleTheme}>
              Ver em {theme === 'light' ? 'escuro' : 'claro'}
            </Button>
          </>
        } />
      

      <Tabs
        className="mb-4"
        value={tab}
        onChange={setTab}
        items={[
        { id: 'cores', label: 'Tokens' },
        { id: 'tipografia', label: 'Tipografia e espaço' },
        { id: 'componentes', label: 'Componentes' }]
        } />
      

      {tab === 'cores' ?
      <div className="space-y-3">
          {colorGroups.map((group) =>
        <Section key={group.title} title={group.title}>
              <ul className="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-6">
                {group.tokens.map((token) =>
            <li key={token.name} className="rounded-md border border-line">
                    <div
                className="h-12 rounded-t-md border-b border-line"
                style={{ background: `var(${token.variable})` }} />
              
                    <div className="px-2 py-1.5">
                      <p className="truncate text-xs font-medium text-fg">{token.name}</p>
                      <p className="truncate text-2xs text-fg-subtle">{token.note ?? token.variable}</p>
                    </div>
                  </li>
            )}
              </ul>
            </Section>
        )}

          <Section title="Raio e elevação" description="Escala plana: tudo entre 6 e 8px. Sem pills, sem blobs.">
            <div className="flex flex-wrap items-end gap-3">
              {[
            { label: 'rounded-md · 6px', className: 'rounded-md' },
            { label: 'rounded-lg · 8px', className: 'rounded-lg' }].
            map((item) =>
            <div key={item.label} className="text-center">
                  <div className={`h-16 w-24 border border-line bg-surface-2 ${item.className}`} />
                  <p className="mt-1.5 text-2xs text-fg-subtle">{item.label}</p>
                </div>
            )}
              <div aria-hidden="true" className="hidden h-16 w-px bg-line sm:block" />
              {[
            { label: 'shadow-xs · cards', className: 'shadow-xs' },
            { label: 'shadow-sm · hover', className: 'shadow-sm' },
            { label: 'shadow-pop · overlays', className: 'shadow-pop' }].
            map((item) =>
            <div key={item.label} className="text-center">
                  <div className={`h-16 w-24 rounded-md border border-line bg-surface ${item.className}`} />
                  <p className="mt-1.5 text-2xs text-fg-subtle">{item.label}</p>
                </div>
            )}
            </div>
          </Section>
        </div> :
      null}

      {tab === 'tipografia' ?
      <div className="grid grid-cols-1 gap-3 lg:grid-cols-2">
          <Section title="Tipografia" description="Sora — geométrica, profissional, hierarquia clara">
            <ul className="space-y-3">
              {typeScale.map((item) =>
            <li key={item.label} className="flex items-baseline justify-between gap-4 border-b border-line pb-3 last:border-0 last:pb-0">
                  <span className={item.className}>{item.label}</span>
                  <span className="shrink-0 text-2xs text-fg-subtle tnum">{item.spec}</span>
                </li>
            )}
            </ul>
          </Section>

          <Section title="Escala de espaçamento" description="Densidade compacta — passos de 2 e 4px">
            <Table>
              <THead>
                <TR hoverable={false}>
                  <TH>Token</TH>
                  <TH>Valor</TH>
                  <TH>Uso</TH>
                  <TH />
                </TR>
              </THead>
              <TBody>
                {spacing.map((item) =>
              <TR key={item.token}>
                    <TD className="tnum font-medium">{item.token}</TD>
                    <TD className="tnum text-fg-muted">{item.px}</TD>
                    <TD className="text-fg-muted">{item.use}</TD>
                    <TD>
                      <span className="block h-2 rounded-md bg-accent" style={{ width: item.px }} />
                    </TD>
                  </TR>
              )}
              </TBody>
            </Table>
          </Section>

          <Section
          className="lg:col-span-2"
          title="Cards vs. secções"
          description="Secção plana com borda hairline por omissão; card apenas para KPI ou interacção">
          
            <div className="grid grid-cols-1 gap-3 sm:grid-cols-3">
              <StatCard label="KPI em card" value="682 000" delta={12.9} deltaLabel="uso correcto" />
              <div className="rounded-md border border-line p-3.5 sm:col-span-2">
                <p className="text-sm font-semibold text-fg">Secção plana</p>
                <p className="mt-1 text-xs leading-5 text-fg-muted">
                  Para listas, tabelas e formulários. Sem sombra, sem card dentro de card, sem decoração.
                </p>
              </div>
            </div>
          </Section>
        </div> :
      null}

      {tab === 'componentes' ?
      <div className="space-y-3">
          <Section title="Botões" description="Estados: normal, hover, activo, foco, desactivado">
            <div className="space-y-3">
              <div className="flex flex-wrap items-center gap-2">
                <Button variant="primary" icon={PlusIcon}>
                  Primário
                </Button>
                <Button variant="primary" className="bg-accent-hover">
                  Hover
                </Button>
                <Button variant="primary" className="bg-accent-active">
                  Activo
                </Button>
                <Button variant="primary" className="ring-2 ring-[color:var(--ring)] ring-offset-2 ring-offset-[color:var(--canvas)]">
                  Foco
                </Button>
                <Button variant="primary" disabled>
                  Desactivado
                </Button>
              </div>
              <div className="flex flex-wrap items-center gap-2">
                <Button>Secundário</Button>
                <Button variant="ghost">Fantasma</Button>
                <Button variant="danger" icon={Trash2Icon}>
                  Eliminar
                </Button>
                <IconButton icon={SearchIcon} label="Pesquisar" />
                <Button size="sm">Pequeno</Button>
                <Button size="xs">Mínimo</Button>
              </div>
            </div>
          </Section>

          <div className="grid grid-cols-1 gap-3 lg:grid-cols-2">
            <Section title="Formulários" description="Altura 36px, texto 13px">
              <div className="space-y-3">
                <FieldGroup label="Nome do curso" required htmlFor="ds-nome">
                  <Input id="ds-nome" placeholder="Ex.: Excel Avançado" />
                </FieldGroup>
                <FieldGroup label="Pesquisa" htmlFor="ds-search">
                  <Input id="ds-search" icon={SearchIcon} placeholder="Pesquisar…" />
                </FieldGroup>
                <FieldGroup label="Área" htmlFor="ds-area">
                  <Select
                  id="ds-area"
                  options={[
                  { value: 'a', label: 'Escritório' },
                  { value: 'b', label: 'Tecnologia' }]
                  } />
                
                </FieldGroup>
                <FieldGroup label="Campo com erro" error="Introduza um preço válido em MZN." htmlFor="ds-erro">
                  <Input id="ds-erro" invalid defaultValue="-100" />
                </FieldGroup>
                <FieldGroup label="Descrição" htmlFor="ds-desc">
                  <Textarea id="ds-desc" rows={3} placeholder="Descrição curta do curso" />
                </FieldGroup>
                <FieldGroup label="Desactivado" htmlFor="ds-dis">
                  <Input id="ds-dis" disabled defaultValue="Bloqueado" />
                </FieldGroup>
                <Checkbox label="Emitir certificado automaticamente" defaultChecked />
              </div>
            </Section>

            <div className="space-y-3">
              <Section title="Badges e estados">
                <div className="flex flex-wrap gap-2">
                  <Badge>Rascunho</Badge>
                  <Badge tone="accent">Novo</Badge>
                  <Badge tone="success" dot>
                    Pago
                  </Badge>
                  <Badge tone="warn" dot>
                    Pendente
                  </Badge>
                  <Badge tone="danger" dot>
                    Falhado
                  </Badge>
                  <Badge tone="info" dot>
                    Reembolsado
                  </Badge>
                </div>
              </Section>

              <Section title="Navegação e controlos">
                <div className="space-y-3">
                  <Segmented
                  value={segment}
                  onChange={setSegment}
                  items={[
                  { id: '7d', label: '7 dias' },
                  { id: '30d', label: '30 dias' },
                  { id: '12m', label: '12 meses' }]
                  } />
                
                  <Tabs
                  value="a"
                  onChange={() => undefined}
                  items={[
                  { id: 'a', label: 'Todos', count: 86 },
                  { id: 'b', label: 'Publicados', count: 74 }]
                  } />
                
                  <div className="flex items-center gap-2">
                    <Dropdown
                    items={[
                    { id: '1', label: 'Editar' },
                    { id: '2', label: 'Duplicar' },
                    { id: '3', label: 'Eliminar', tone: 'danger' }]
                    }
                    trigger={({ toggle }) =>
                    <Button size="sm" onClick={toggle}>
                          Menu suspenso
                        </Button>
                    } />
                  
                    <Button size="sm" onClick={() => setModalOpen(true)}>
                      Abrir modal
                    </Button>
                  </div>
                  <Pagination page={page} pageCount={4} total={86} pageSize={20} onPageChange={setPage} />
                </div>
              </Section>

              <Section title="Progresso e identidade">
                <div className="flex items-center gap-4">
                  <div className="flex items-center gap-1.5">
                    <Avatar name="Ana Chirindza" size="sm" />
                    <Avatar name="Délcio Nhaca" />
                    <Avatar name="Sérgio Macuácua" size="lg" />
                  </div>
                  <ProgressBar value={68} label="Progresso" className="flex-1" />
                </div>
              </Section>
            </div>
          </div>

          <Section title="Stepper compacto" description="Segmentos rounded-md, sem pills nem glow">
            <Stepper
            steps={[
            { id: '1', label: 'Informação', hint: 'Título e área' },
            { id: '2', label: 'Conteúdo', hint: 'Módulos' },
            { id: '3', label: 'Preço', hint: 'MZN' },
            { id: '4', label: 'Revisão', hint: 'Publicar' }]
            }
            current={step}
            onStepChange={setStep} />
          
          </Section>

          <div className="grid grid-cols-1 gap-3 lg:grid-cols-2">
            <Section title="Zona de upload">
              <UploadZone />
            </Section>
            <Section title="Notificações (toasts)">
              <div className="space-y-2">
                <Toast tone="success" title="Curso publicado" description="Visível no catálogo." onClose={() => undefined} />
                <Toast tone="danger" title="Pagamento falhado" description="Cartão recusado — CRD-77310." onClose={() => undefined} />
              </div>
            </Section>
          </div>

          <div className="grid grid-cols-1 gap-3 lg:grid-cols-2">
            <Section title="Estado vazio" bodyClassName="p-0">
              <EmptyState
              icon={BookOpenIcon}
              title="Ainda não tem cursos"
              description="Quando se inscrever num curso, ele aparece aqui com progresso e certificados."
              action={
              <Button variant="primary" size="sm">
                    Explorar catálogo
                  </Button>
              } />
            
            </Section>
            <Section title="Carregamento (skeleton)">
              <div className="grid grid-cols-2 gap-3">
                <StatCardSkeleton />
                <StatCardSkeleton />
              </div>
              <div className="mt-3 space-y-2">
                <Skeleton className="h-3 w-full" />
                <Skeleton className="h-3 w-4/5" />
                <Skeleton className="h-3 w-2/3" />
              </div>
            </Section>
          </div>

          <Section title="Tabela" description="Linhas de 36px, cabeçalho em surface-2" bodyClassName="p-0">
            <Table>
              <THead>
                <TR hoverable={false}>
                  <TH>Curso</TH>
                  <TH>Formador</TH>
                  <TH align="right">Alunos</TH>
                  <TH>Estado</TH>
                </TR>
              </THead>
              <TBody>
                {[
              ['Excel Avançado para Gestão', 'Ana Chirindza', '428'],
              ['Introdução à Programação Web', 'Nélson Matola', '592']].
              map((row) =>
              <TR key={row[0]}>
                    <TD className="font-medium">{row[0]}</TD>
                    <TD className="text-fg-muted">{row[1]}</TD>
                    <TD align="right" className="tnum text-fg-muted">
                      {row[2]}
                    </TD>
                    <TD>
                      <Badge tone="success" dot>
                        Publicado
                      </Badge>
                    </TD>
                  </TR>
              )}
              </TBody>
            </Table>
          </Section>

          <Card>
            <p className="text-sm font-semibold text-fg">Links da barra lateral</p>
            <p className="mt-0.5 text-xs text-fg-muted">Estados: normal, hover, activo, foco.</p>
            <ul className="mt-3 max-w-xs space-y-0.5">
              {[
            { label: 'Normal', className: 'text-fg-muted' },
            { label: 'Hover', className: 'bg-surface-2 text-fg' },
            { label: 'Activo', className: 'bg-accent-soft text-accent' },
            {
              label: 'Foco',
              className:
              'text-fg-muted ring-2 ring-[color:var(--ring)] ring-offset-2 ring-offset-[color:var(--surface)]'
            }].
            map((item) =>
            <li
              key={item.label}
              className={`flex items-center gap-2.5 rounded-md px-2 py-1.5 text-sm font-medium ${item.className}`}>
              
                  <BookOpenIcon className="h-4 w-4" />
                  {item.label}
                </li>
            )}
            </ul>
          </Card>
        </div> :
      null}

      <Modal
        open={modalOpen}
        onClose={() => setModalOpen(false)}
        title="Confirmar publicação"
        description="O curso ficará visível no catálogo."
        footer={
        <>
            <Button size="sm" onClick={() => setModalOpen(false)}>
              Cancelar
            </Button>
            <Button size="sm" variant="primary" onClick={() => setModalOpen(false)}>
              Publicar
            </Button>
          </>
        }>
        
        Os alunos inscritos receberão uma notificação por email e SMS.
      </Modal>
    </>);

}