export interface MessageThread {
  id: string;
  student: string;
  course: string;
  preview: string;
  time: string;
  unread: boolean;
}

export const messageThreads: MessageThread[] = [
{
  id: 'mt1',
  student: 'Jorge Cuna',
  course: 'Excel Avançado',
  preview: 'Bom dia, a fórmula PROCX não funciona na versão 2016?',
  time: '09:41',
  unread: true
},
{
  id: 'mt2',
  student: 'Célia Bila',
  course: 'Excel Avançado',
  preview: 'Já submeti o trabalho do módulo 3, obrigada!',
  time: '08:12',
  unread: true
},
{
  id: 'mt3',
  student: 'Nádia Ferrão',
  course: 'Marketing Digital',
  preview: 'Consigo pagar a segunda parte por M-Pesa?',
  time: 'Ontem',
  unread: false
},
{
  id: 'mt4',
  student: 'Délcio Nhaca',
  course: 'Marketing Digital',
  preview: 'A aula ao vivo vai ficar gravada?',
  time: 'Ontem',
  unread: false
},
{
  id: 'mt5',
  student: 'Amélia Tembe',
  course: 'Excel Avançado',
  preview: 'Não recebi o link de acesso.',
  time: '11 Ago',
  unread: false
}];


export interface Message {
  id: string;
  from: 'aluno' | 'formador';
  body: string;
  time: string;
}

export const conversation: Message[] = [
{
  id: 'c1',
  from: 'aluno',
  body: 'Bom dia, a fórmula PROCX não funciona na versão 2016?',
  time: '09:41'
},
{
  id: 'c2',
  from: 'formador',
  body: 'Bom dia, Jorge. Não, o PROCX só existe a partir do Office 365. Use PROCV no exercício.',
  time: '09:48'
},
{
  id: 'c3',
  from: 'aluno',
  body: 'Perfeito, vou refazer com PROCV. Obrigado!',
  time: '09:52'
}];