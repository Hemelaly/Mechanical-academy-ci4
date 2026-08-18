export type PersonStatus = 'activo' | 'inactivo' | 'suspenso' | 'pendente';

export interface Student {
  id: string;
  name: string;
  email: string;
  phone: string;
  courses: number;
  progress: number;
  lastActive: string;
  status: PersonStatus;
}

export const students: Student[] = [
{
  id: 's1',
  name: 'Délcio Nhaca',
  email: 'delcio.nhaca@gmail.com',
  phone: '+258 84 512 8830',
  courses: 4,
  progress: 68,
  lastActive: 'há 4 min',
  status: 'activo'
},
{
  id: 's2',
  name: 'Célia Bila',
  email: 'celia.bila@gmail.com',
  phone: '+258 82 337 1204',
  courses: 3,
  progress: 92,
  lastActive: 'há 1 h',
  status: 'activo'
},
{
  id: 's3',
  name: 'Jorge Cuna',
  email: 'jorge.cuna@outlook.com',
  phone: '+258 87 904 5512',
  courses: 2,
  progress: 41,
  lastActive: 'há 3 h',
  status: 'activo'
},
{
  id: 's4',
  name: 'Nádia Ferrão',
  email: 'nadia.ferrao@gmail.com',
  phone: '+258 84 220 7745',
  courses: 1,
  progress: 24,
  lastActive: 'ontem',
  status: 'activo'
},
{
  id: 's5',
  name: 'Ivo Chissano',
  email: 'ivo.chissano@gmail.com',
  phone: '+258 86 118 9032',
  courses: 1,
  progress: 8,
  lastActive: 'há 5 dias',
  status: 'inactivo'
},
{
  id: 's6',
  name: 'Rosa Mabjaia',
  email: 'rosa.mabjaia@academy.co.mz',
  phone: '+258 84 771 3390',
  courses: 2,
  progress: 55,
  lastActive: 'há 2 dias',
  status: 'activo'
},
{
  id: 's7',
  name: 'Hélder Sitoe',
  email: 'helder.sitoe@gmail.com',
  phone: '+258 82 664 2218',
  courses: 5,
  progress: 77,
  lastActive: 'há 8 h',
  status: 'activo'
},
{
  id: 's8',
  name: 'Amélia Tembe',
  email: 'amelia.tembe@gmail.com',
  phone: '+258 87 332 6641',
  courses: 1,
  progress: 0,
  lastActive: 'nunca',
  status: 'pendente'
},
{
  id: 's9',
  name: 'Bruno Macamo',
  email: 'bruno.macamo@gmail.com',
  phone: '+258 84 905 1177',
  courses: 2,
  progress: 33,
  lastActive: 'há 12 dias',
  status: 'suspenso'
}];


export interface Instructor {
  id: string;
  name: string;
  email: string;
  courses: number;
  students: number;
  revenue: number;
  rating: number;
  status: PersonStatus;
}

export const instructors: Instructor[] = [
{
  id: 'f1',
  name: 'Ana Chirindza',
  email: 'ana.chirindza@academy.co.mz',
  courses: 3,
  students: 744,
  revenue: 2081200,
  rating: 4.7,
  status: 'activo'
},
{
  id: 'f2',
  name: 'Nélson Matola',
  email: 'nelson.matola@academy.co.mz',
  courses: 2,
  students: 633,
  revenue: 2725500,
  rating: 4.8,
  status: 'activo'
},
{
  id: 'f3',
  name: 'Rosa Mabjaia',
  email: 'rosa.mabjaia@academy.co.mz',
  courses: 1,
  students: 187,
  revenue: 561000,
  rating: 4.4,
  status: 'activo'
},
{
  id: 'f4',
  name: 'Jorge Cuna',
  email: 'jorge.cuna@academy.co.mz',
  courses: 1,
  students: 233,
  revenue: 512600,
  rating: 4.3,
  status: 'activo'
},
{
  id: 'f5',
  name: 'Hélder Sitoe',
  email: 'helder.sitoe@academy.co.mz',
  courses: 1,
  students: 0,
  revenue: 0,
  rating: 0,
  status: 'pendente'
},
{
  id: 'f6',
  name: 'Célia Bila',
  email: 'celia.bila@academy.co.mz',
  courses: 1,
  students: 74,
  revenue: 281200,
  rating: 4.1,
  status: 'inactivo'
}];