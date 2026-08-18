import {
  AwardIcon,
  BarChart3Icon,
  BookOpenIcon,
  CompassIcon,
  GraduationCapIcon,
  LayoutGridIcon,
  LifeBuoyIcon,
  type LucideIcon,
  MessageSquareIcon,
  SettingsIcon,
  UserRoundIcon,
  UsersIcon,
  VideoIcon,
  WalletIcon } from
'lucide-react';

export type Role = 'admin' | 'instructor' | 'student';

export interface NavItem {
  label: string;
  to: string;
  icon: LucideIcon;
  badge?: string;
}

export interface RoleConfig {
  role: Role;
  /** Portuguese role label shown in the sidebar footer and role switcher. */
  label: string;
  home: string;
  nav: NavItem[];
  secondary: NavItem[];
  user: {name: string;email: string;};
}

export const roleConfigs: Record<Role, RoleConfig> = {
  admin: {
    role: 'admin',
    label: 'Administrador',
    home: '/admin',
    nav: [
    { label: 'Painel', to: '/admin', icon: LayoutGridIcon },
    { label: 'Cursos', to: '/admin/cursos', icon: BookOpenIcon },
    { label: 'Alunos', to: '/admin/alunos', icon: UsersIcon },
    { label: 'Formadores', to: '/admin/formadores', icon: GraduationCapIcon },
    { label: 'Financeiro', to: '/admin/financeiro', icon: WalletIcon, badge: '4' },
    { label: 'Relatórios', to: '/admin/relatorios', icon: BarChart3Icon },
    { label: 'Certificados', to: '/admin/certificados', icon: AwardIcon }],

    secondary: [
    { label: 'Configurações', to: '/admin/configuracoes', icon: SettingsIcon },
    { label: 'Suporte', to: '/admin/suporte', icon: LifeBuoyIcon }],

    user: { name: 'Sérgio Macuácua', email: 'sergio@academy.co.mz' }
  },
  instructor: {
    role: 'instructor',
    label: 'Formador',
    home: '/instrutor',
    nav: [
    { label: 'Painel', to: '/instrutor', icon: LayoutGridIcon },
    { label: 'Meus cursos', to: '/instrutor/cursos', icon: BookOpenIcon },
    { label: 'Alunos', to: '/instrutor/alunos', icon: UsersIcon },
    { label: 'Aulas ao vivo', to: '/instrutor/aulas', icon: VideoIcon },
    { label: 'Ganhos', to: '/instrutor/ganhos', icon: WalletIcon },
    { label: 'Certificados', to: '/instrutor/certificados', icon: AwardIcon },
    { label: 'Mensagens', to: '/instrutor/mensagens', icon: MessageSquareIcon, badge: '9' }],

    secondary: [
    { label: 'Configurações', to: '/instrutor/configuracoes', icon: SettingsIcon },
    { label: 'Suporte', to: '/instrutor/suporte', icon: LifeBuoyIcon }],

    user: { name: 'Ana Chirindza', email: 'ana.chirindza@academy.co.mz' }
  },
  student: {
    role: 'student',
    label: 'Aluno',
    home: '/aluno',
    nav: [
    { label: 'Painel', to: '/aluno', icon: LayoutGridIcon },
    { label: 'Meus cursos', to: '/aluno/cursos', icon: BookOpenIcon },
    { label: 'Catálogo', to: '/aluno/catalogo', icon: CompassIcon },
    { label: 'Aulas ao vivo', to: '/aluno/aulas', icon: VideoIcon },
    { label: 'Certificados', to: '/aluno/certificados', icon: AwardIcon },
    { label: 'Perfil', to: '/aluno/perfil', icon: UserRoundIcon }],

    secondary: [
    { label: 'Configurações', to: '/aluno/configuracoes', icon: SettingsIcon },
    { label: 'Suporte', to: '/aluno/suporte', icon: LifeBuoyIcon }],

    user: { name: 'Délcio Nhaca', email: 'delcio.nhaca@gmail.com' }
  }
};