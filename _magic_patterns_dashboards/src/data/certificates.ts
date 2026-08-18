export type CertificateStatus = 'emitido' | 'pendente' | 'revogado';

export interface Certificate {
  id: string;
  code: string;
  student: string;
  course: string;
  instructor: string;
  issued: string;
  grade: number;
  status: CertificateStatus;
}

export const certificates: Certificate[] = [
{
  id: 'k1',
  code: 'AC-2026-0481',
  student: 'Délcio Nhaca',
  course: 'Excel Básico',
  instructor: 'Ana Chirindza',
  issued: '02 Jul 2026',
  grade: 88,
  status: 'emitido'
},
{
  id: 'k2',
  code: 'AC-2026-0479',
  student: 'Célia Bila',
  course: 'Excel Avançado para Gestão',
  instructor: 'Ana Chirindza',
  issued: '28 Jun 2026',
  grade: 95,
  status: 'emitido'
},
{
  id: 'k3',
  code: 'AC-2026-0466',
  student: 'Hélder Sitoe',
  course: 'Introdução à Programação Web',
  instructor: 'Nélson Matola',
  issued: '19 Jun 2026',
  grade: 91,
  status: 'emitido'
},
{
  id: 'k4',
  code: 'AC-2026-0502',
  student: 'Jorge Cuna',
  course: 'Segurança e Higiene no Trabalho',
  instructor: 'Jorge Cuna',
  issued: '—',
  grade: 74,
  status: 'pendente'
},
{
  id: 'k5',
  code: 'AC-2026-0312',
  student: 'Délcio Nhaca',
  course: 'Atendimento ao Cliente',
  instructor: 'Rosa Mabjaia',
  issued: '18 Mai 2026',
  grade: 82,
  status: 'emitido'
},
{
  id: 'k6',
  code: 'AC-2026-0288',
  student: 'Bruno Macamo',
  course: 'Fotografia com Telemóvel',
  instructor: 'Nélson Matola',
  issued: '04 Mai 2026',
  grade: 68,
  status: 'revogado'
}];