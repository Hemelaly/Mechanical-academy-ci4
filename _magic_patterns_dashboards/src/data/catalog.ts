export type CatalogLevel = 'Iniciante' | 'Intermédio' | 'Avançado';

export interface CatalogCourse {
  id: string;
  title: string;
  category: string;
  instructor: string;
  level: CatalogLevel;
  duration: string;
  lessons: number;
  price: number;
  rating: number;
  students: number;
  enrolled: boolean;
}

export const catalogCourses: CatalogCourse[] = [
{
  id: 'g1',
  title: 'Excel Avançado para Gestão',
  category: 'Escritório',
  instructor: 'Ana Chirindza',
  level: 'Avançado',
  duration: '2 h 58 min',
  lessons: 15,
  price: 2500,
  rating: 4.8,
  students: 428,
  enrolled: true
},
{
  id: 'g2',
  title: 'Introdução à Programação Web',
  category: 'Tecnologia',
  instructor: 'Nélson Matola',
  level: 'Iniciante',
  duration: '8 h 10 min',
  lessons: 42,
  price: 4500,
  rating: 4.9,
  students: 592,
  enrolled: true
},
{
  id: 'g3',
  title: 'Contabilidade Básica',
  category: 'Finanças',
  instructor: 'Hélder Sitoe',
  level: 'Iniciante',
  duration: '3 h 25 min',
  lessons: 18,
  price: 2800,
  rating: 0,
  students: 0,
  enrolled: false
},
{
  id: 'g4',
  title: 'Segurança e Higiene no Trabalho',
  category: 'Indústria',
  instructor: 'Jorge Cuna',
  level: 'Intermédio',
  duration: '4 h 02 min',
  lessons: 22,
  price: 2200,
  rating: 4.3,
  students: 233,
  enrolled: false
},
{
  id: 'g5',
  title: 'Gestão de Recursos Humanos',
  category: 'Negócios',
  instructor: 'Célia Bila',
  level: 'Intermédio',
  duration: '5 h 15 min',
  lessons: 26,
  price: 3800,
  rating: 4.1,
  students: 74,
  enrolled: false
},
{
  id: 'g6',
  title: 'Inglês Técnico para Engenharia',
  category: 'Idiomas',
  instructor: 'Rosa Mabjaia',
  level: 'Intermédio',
  duration: '6 h 40 min',
  lessons: 30,
  price: 3000,
  rating: 4.4,
  students: 187,
  enrolled: true
},
{
  id: 'g7',
  title: 'Marketing Digital para PMEs',
  category: 'Negócios',
  instructor: 'Ana Chirindza',
  level: 'Iniciante',
  duration: '4 h 30 min',
  lessons: 21,
  price: 3200,
  rating: 4.6,
  students: 316,
  enrolled: true
},
{
  id: 'g8',
  title: 'Fotografia com Telemóvel',
  category: 'Criativo',
  instructor: 'Nélson Matola',
  level: 'Iniciante',
  duration: '1 h 50 min',
  lessons: 12,
  price: 1500,
  rating: 3.9,
  students: 41,
  enrolled: false
}];