export type CourseStatus = 'publicado' | 'rascunho' | 'revisao' | 'arquivado';

export interface Course {
  id: string;
  title: string;
  category: string;
  instructor: string;
  students: number;
  price: number;
  rating: number;
  status: CourseStatus;
  updated: string;
}

export const courses: Course[] = [
{
  id: 'c1',
  title: 'Excel Avançado para Gestão',
  category: 'Escritório',
  instructor: 'Ana Chirindza',
  students: 428,
  price: 2500,
  rating: 4.8,
  status: 'publicado',
  updated: '12 Ago 2026'
},
{
  id: 'c2',
  title: 'Marketing Digital para PMEs',
  category: 'Negócios',
  instructor: 'Ana Chirindza',
  students: 316,
  price: 3200,
  rating: 4.6,
  status: 'publicado',
  updated: '11 Ago 2026'
},
{
  id: 'c3',
  title: 'Contabilidade Básica',
  category: 'Finanças',
  instructor: 'Hélder Sitoe',
  students: 0,
  price: 2800,
  rating: 0,
  status: 'revisao',
  updated: '11 Ago 2026'
},
{
  id: 'c4',
  title: 'Introdução à Programação Web',
  category: 'Tecnologia',
  instructor: 'Nélson Matola',
  students: 592,
  price: 4500,
  rating: 4.9,
  status: 'publicado',
  updated: '09 Ago 2026'
},
{
  id: 'c5',
  title: 'Inglês Técnico para Engenharia',
  category: 'Idiomas',
  instructor: 'Rosa Mabjaia',
  students: 187,
  price: 3000,
  rating: 4.4,
  status: 'publicado',
  updated: '08 Ago 2026'
},
{
  id: 'c6',
  title: 'Gestão de Recursos Humanos',
  category: 'Negócios',
  instructor: 'Célia Bila',
  students: 74,
  price: 3800,
  rating: 4.1,
  status: 'rascunho',
  updated: '05 Ago 2026'
},
{
  id: 'c7',
  title: 'Segurança e Higiene no Trabalho',
  category: 'Indústria',
  instructor: 'Jorge Cuna',
  students: 233,
  price: 2200,
  rating: 4.3,
  status: 'publicado',
  updated: '02 Ago 2026'
},
{
  id: 'c8',
  title: 'Fotografia com Telemóvel',
  category: 'Criativo',
  instructor: 'Nélson Matola',
  students: 41,
  price: 1500,
  rating: 3.9,
  status: 'arquivado',
  updated: '28 Jul 2026'
}];


export const popularCourses = [
{ id: 'p1', title: 'Introdução à Programação Web', enrollments: 592, revenue: 2664000, trend: 12.4 },
{ id: 'p2', title: 'Excel Avançado para Gestão', enrollments: 428, revenue: 1070000, trend: 8.1 },
{ id: 'p3', title: 'Marketing Digital para PMEs', enrollments: 316, revenue: 1011200, trend: 4.7 },
{ id: 'p4', title: 'Segurança e Higiene no Trabalho', enrollments: 233, revenue: 512600, trend: -2.3 },
{ id: 'p5', title: 'Inglês Técnico para Engenharia', enrollments: 187, revenue: 561000, trend: 1.8 }];


export interface EnrolledCourse {
  id: string;
  title: string;
  instructor: string;
  progress: number;
  nextLesson: string;
  duration: string;
  modulesDone: number;
  modulesTotal: number;
}

export const enrolledCourses: EnrolledCourse[] = [
{
  id: 'e1',
  title: 'Excel Avançado para Gestão',
  instructor: 'Ana Chirindza',
  progress: 68,
  nextLesson: 'Aula 12 — Tabelas dinâmicas',
  duration: '14 min',
  modulesDone: 8,
  modulesTotal: 12
},
{
  id: 'e2',
  title: 'Marketing Digital para PMEs',
  instructor: 'Ana Chirindza',
  progress: 34,
  nextLesson: 'Aula 5 — Campanhas locais',
  duration: '22 min',
  modulesDone: 3,
  modulesTotal: 9
},
{
  id: 'e3',
  title: 'Introdução à Programação Web',
  instructor: 'Nélson Matola',
  progress: 12,
  nextLesson: 'Aula 2 — Estrutura HTML',
  duration: '18 min',
  modulesDone: 1,
  modulesTotal: 16
},
{
  id: 'e4',
  title: 'Inglês Técnico para Engenharia',
  instructor: 'Rosa Mabjaia',
  progress: 91,
  nextLesson: 'Aula 15 — Relatórios técnicos',
  duration: '9 min',
  modulesDone: 14,
  modulesTotal: 15
}];


export interface LiveClass {
  id: string;
  title: string;
  course: string;
  date: string;
  time: string;
  instructor: string;
}

export const liveClasses: LiveClass[] = [
{
  id: 'l1',
  title: 'Sessão de dúvidas — Fórmulas',
  course: 'Excel Avançado',
  date: 'Hoje',
  time: '18:00',
  instructor: 'Ana Chirindza'
},
{
  id: 'l2',
  title: 'Workshop de campanhas',
  course: 'Marketing Digital',
  date: 'Amanhã',
  time: '19:30',
  instructor: 'Ana Chirindza'
},
{
  id: 'l3',
  title: 'Revisão do Módulo 1',
  course: 'Programação Web',
  date: 'Sex, 15 Ago',
  time: '17:00',
  instructor: 'Nélson Matola'
}];