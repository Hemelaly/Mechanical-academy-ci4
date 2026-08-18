import React from 'react';
import { BrowserRouter, Navigate, Route, Routes } from 'react-router-dom';
import { ThemeProvider } from './contexts/ThemeContext';
import { AppShell } from './components/shell/AppShell';
import { DesignSystem } from './pages/DesignSystem';
import { Settings } from './pages/shared/Settings';
import { Support } from './pages/shared/Support';
import { AdminHome } from './pages/admin/AdminHome';
import { AdminCourses } from './pages/admin/AdminCourses';
import { AdminFinance } from './pages/admin/AdminFinance';
import { AdminStudents } from './pages/admin/AdminStudents';
import { AdminInstructors } from './pages/admin/AdminInstructors';
import { AdminReports } from './pages/admin/AdminReports';
import { AdminCertificates } from './pages/admin/AdminCertificates';
import { InstructorHome } from './pages/instructor/InstructorHome';
import { InstructorCourses } from './pages/instructor/InstructorCourses';
import { InstructorStudents } from './pages/instructor/InstructorStudents';
import { InstructorLive } from './pages/instructor/InstructorLive';
import { InstructorEarnings } from './pages/instructor/InstructorEarnings';
import { InstructorCertificates } from './pages/instructor/InstructorCertificates';
import { InstructorMessages } from './pages/instructor/InstructorMessages';
import { CourseWizard } from './pages/instructor/CourseWizard';
import { StudentHome } from './pages/student/StudentHome';
import { StudentCourses } from './pages/student/StudentCourses';
import { StudentCatalog } from './pages/student/StudentCatalog';
import { StudentLive } from './pages/student/StudentLive';
import { StudentCertificates } from './pages/student/StudentCertificates';
import { StudentProfile } from './pages/student/StudentProfile';

export function App() {
  return (
    <ThemeProvider>
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<Navigate to="/admin" replace />} />

          <Route element={<AppShell role="admin" />}>
            <Route path="/admin" element={<AdminHome />} />
            <Route path="/admin/cursos" element={<AdminCourses />} />
            <Route path="/admin/alunos" element={<AdminStudents />} />
            <Route path="/admin/formadores" element={<AdminInstructors />} />
            <Route path="/admin/financeiro" element={<AdminFinance />} />
            <Route path="/admin/relatorios" element={<AdminReports />} />
            <Route path="/admin/certificados" element={<AdminCertificates />} />
            <Route path="/admin/configuracoes" element={<Settings role="admin" />} />
            <Route path="/admin/suporte" element={<Support />} />
            <Route path="/design-system" element={<DesignSystem />} />
          </Route>

          <Route element={<AppShell role="instructor" />}>
            <Route path="/instrutor" element={<InstructorHome />} />
            <Route path="/instrutor/cursos" element={<InstructorCourses />} />
            <Route path="/instrutor/cursos/novo" element={<CourseWizard />} />
            <Route path="/instrutor/alunos" element={<InstructorStudents />} />
            <Route path="/instrutor/aulas" element={<InstructorLive />} />
            <Route path="/instrutor/ganhos" element={<InstructorEarnings />} />
            <Route path="/instrutor/certificados" element={<InstructorCertificates />} />
            <Route path="/instrutor/mensagens" element={<InstructorMessages />} />
            <Route path="/instrutor/configuracoes" element={<Settings role="instructor" />} />
            <Route path="/instrutor/suporte" element={<Support />} />
          </Route>

          <Route element={<AppShell role="student" />}>
            <Route path="/aluno" element={<StudentHome />} />
            <Route path="/aluno/cursos" element={<StudentCourses />} />
            <Route path="/aluno/catalogo" element={<StudentCatalog />} />
            <Route path="/aluno/aulas" element={<StudentLive />} />
            <Route path="/aluno/certificados" element={<StudentCertificates />} />
            <Route path="/aluno/perfil" element={<StudentProfile />} />
            <Route path="/aluno/configuracoes" element={<Settings role="student" />} />
            <Route path="/aluno/suporte" element={<Support />} />
          </Route>

          <Route path="*" element={<Navigate to="/admin" replace />} />
        </Routes>
      </BrowserRouter>
    </ThemeProvider>);

}