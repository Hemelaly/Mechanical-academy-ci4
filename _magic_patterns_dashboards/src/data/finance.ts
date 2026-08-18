export type PaymentStatus = 'pago' | 'pendente' | 'falhado' | 'reembolsado';
export type PaymentMethod = 'M-Pesa' | 'e-Mola' | 'Transferência' | 'Cartão';

export interface Transaction {
  id: string;
  reference: string;
  student: string;
  course: string;
  method: PaymentMethod;
  amount: number;
  status: PaymentStatus;
  date: string;
}

export const transactions: Transaction[] = [
{
  id: 't1',
  reference: 'MPS-84213',
  student: 'Délcio Nhaca',
  course: 'Marketing Digital para PMEs',
  method: 'M-Pesa',
  amount: 3200,
  status: 'pago',
  date: '13 Ago, 09:42'
},
{
  id: 't2',
  reference: 'MPS-84208',
  student: 'Célia Bila',
  course: 'Excel Avançado para Gestão',
  method: 'M-Pesa',
  amount: 2500,
  status: 'pago',
  date: '13 Ago, 08:15'
},
{
  id: 't3',
  reference: 'EMO-11907',
  student: 'Jorge Cuna',
  course: 'Introdução à Programação Web',
  method: 'e-Mola',
  amount: 4500,
  status: 'pendente',
  date: '13 Ago, 07:58'
},
{
  id: 't4',
  reference: 'TRF-00542',
  student: 'Construtora Beira, Lda',
  course: 'Segurança e Higiene (12 licenças)',
  method: 'Transferência',
  amount: 26400,
  status: 'pendente',
  date: '12 Ago, 16:30'
},
{
  id: 't5',
  reference: 'MPS-84190',
  student: 'Rosa Mabjaia',
  course: 'Inglês Técnico para Engenharia',
  method: 'M-Pesa',
  amount: 3000,
  status: 'reembolsado',
  date: '12 Ago, 14:02'
},
{
  id: 't6',
  reference: 'CRD-77310',
  student: 'Hélder Sitoe',
  course: 'Contabilidade Básica',
  method: 'Cartão',
  amount: 2800,
  status: 'falhado',
  date: '12 Ago, 11:47'
},
{
  id: 't7',
  reference: 'MPS-84177',
  student: 'Nádia Ferrão',
  course: 'Excel Avançado para Gestão',
  method: 'M-Pesa',
  amount: 2500,
  status: 'pago',
  date: '11 Ago, 19:20'
},
{
  id: 't8',
  reference: 'MPS-84165',
  student: 'Ivo Chissano',
  course: 'Fotografia com Telemóvel',
  method: 'M-Pesa',
  amount: 1500,
  status: 'pago',
  date: '11 Ago, 15:05'
}];


export const revenueSeries = [
{ month: 'Fev', receita: 386000, meta: 400000 },
{ month: 'Mar', receita: 421000, meta: 420000 },
{ month: 'Abr', receita: 398000, meta: 440000 },
{ month: 'Mai', receita: 512000, meta: 460000 },
{ month: 'Jun', receita: 549000, meta: 500000 },
{ month: 'Jul', receita: 604000, meta: 540000 },
{ month: 'Ago', receita: 682000, meta: 580000 }];


export const enrollmentSeries = [
{ week: 'S28', novas: 128, concluidas: 62 },
{ week: 'S29', novas: 164, concluidas: 71 },
{ week: 'S30', novas: 142, concluidas: 88 },
{ week: 'S31', novas: 196, concluidas: 94 },
{ week: 'S32', novas: 214, concluidas: 102 },
{ week: 'S33', novas: 247, concluidas: 118 }];


export const instructorEarningsSeries = [
{ month: 'Mar', ganhos: 42000 },
{ month: 'Abr', ganhos: 38500 },
{ month: 'Mai', ganhos: 51200 },
{ month: 'Jun', ganhos: 47800 },
{ month: 'Jul', ganhos: 63400 },
{ month: 'Ago', ganhos: 71900 }];


export const payoutBreakdown = [
{ label: 'M-Pesa', amount: 486300, share: 71 },
{ label: 'e-Mola', amount: 89400, share: 13 },
{ label: 'Transferência bancária', amount: 82100, share: 12 },
{ label: 'Cartão', amount: 24200, share: 4 }];