export interface NotificationItem {
  id: string;
  title: string;
  unread: boolean;
}

export const notifications: NotificationItem[] = [
{ id: 'n1', title: 'Pagamento M-Pesa recebido — 4 500 MZN', unread: true },
{ id: 'n2', title: '3 novos alunos inscritos em Excel Avançado', unread: true },
{ id: 'n3', title: 'Curso "Contabilidade Básica" aguarda aprovação', unread: true },
{ id: 'n4', title: 'Certificado emitido para Délcio Nhaca', unread: false },
{ id: 'n5', title: 'Aula ao vivo de amanhã às 18:00 confirmada', unread: false }];


export type ActivityTone = 'accent' | 'success' | 'warn' | 'info';

export interface ActivityItem {
  id: string;
  actor: string;
  action: string;
  target: string;
  time: string;
  tone: ActivityTone;
}

export const adminActivity: ActivityItem[] = [
{
  id: 'a1',
  actor: 'Délcio Nhaca',
  action: 'inscreveu-se em',
  target: 'Marketing Digital para PMEs',
  time: 'há 4 min',
  tone: 'accent'
},
{
  id: 'a2',
  actor: 'M-Pesa',
  action: 'confirmou pagamento de',
  target: '4 500 MZN',
  time: 'há 22 min',
  tone: 'success'
},
{
  id: 'a3',
  actor: 'Ana Chirindza',
  action: 'submeteu para revisão',
  target: 'Contabilidade Básica',
  time: 'há 1 h',
  tone: 'warn'
},
{
  id: 'a4',
  actor: 'Sistema',
  action: 'emitiu certificado de',
  target: 'Excel Avançado',
  time: 'há 2 h',
  tone: 'info'
},
{
  id: 'a5',
  actor: 'Rosa Mabjaia',
  action: 'cancelou a assinatura',
  target: 'Plano Anual',
  time: 'há 3 h',
  tone: 'warn'
},
{
  id: 'a6',
  actor: 'Hélder Sitoe',
  action: 'concluiu',
  target: 'Introdução à Programação',
  time: 'há 5 h',
  tone: 'success'
}];


export const instructorActivity: ActivityItem[] = [
{
  id: 'i1',
  actor: 'Célia Bila',
  action: 'entregou o trabalho de',
  target: 'Módulo 3 — Tabelas dinâmicas',
  time: 'há 12 min',
  tone: 'accent'
},
{
  id: 'i2',
  actor: 'M-Pesa',
  action: 'transferiu ganhos de',
  target: '18 200 MZN',
  time: 'há 1 h',
  tone: 'success'
},
{
  id: 'i3',
  actor: 'Jorge Cuna',
  action: 'colocou uma pergunta em',
  target: 'Aula 8 — Fórmulas',
  time: 'há 3 h',
  tone: 'info'
},
{
  id: 'i4',
  actor: '4 alunos',
  action: 'estão em atraso em',
  target: 'Marketing Digital',
  time: 'hoje',
  tone: 'warn'
}];


export interface QuickAction {
  id: string;
  label: string;
  description: string;
  to: string;
}

export const adminQuickActions: QuickAction[] = [
{ id: 'q1', label: 'Novo curso', description: 'Criar e publicar', to: '/instrutor/cursos/novo' },
{ id: 'q2', label: 'Convidar formador', description: 'Enviar acesso', to: '/admin/formadores' },
{ id: 'q3', label: 'Aprovar pagamentos', description: '4 pendentes', to: '/admin/financeiro' },
{ id: 'q4', label: 'Exportar relatório', description: 'CSV / PDF', to: '/admin/relatorios' }];