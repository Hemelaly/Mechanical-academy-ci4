import React from 'react';
import { MoonIcon, SaveIcon, SunIcon } from 'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { Button } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { Checkbox, FieldGroup, Input, Select } from '../../components/ui/Field';
import { Segmented } from '../../components/ui/Tabs';
import { useTheme } from '../../contexts/ThemeContext';
import { roleConfigs, type Role } from '../../data/nav';

export function Settings({ role }: {role: Role;}) {
  const config = roleConfigs[role];
  const { theme, toggleTheme } = useTheme();

  return (
    <>
      <PageHeader
        title="Configurações"
        actions={
        <Button size="sm" variant="primary" icon={SaveIcon}>
            Guardar
          </Button>
        } />
      

      <div className="grid grid-cols-1 gap-3 xl:grid-cols-3">
        <Section className="xl:col-span-2" title="Conta">
          <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
            <FieldGroup label="Nome" htmlFor="st-nome">
              <Input id="st-nome" defaultValue={config.user.name} />
            </FieldGroup>
            <FieldGroup label="Email" htmlFor="st-email">
              <Input id="st-email" type="email" defaultValue={config.user.email} />
            </FieldGroup>
            <FieldGroup label="Idioma" htmlFor="st-idioma">
              <Select
                id="st-idioma"
                options={[
                { value: 'pt', label: 'Português' },
                { value: 'en', label: 'Inglês' }]
                } />
              
            </FieldGroup>
            <FieldGroup label="Fuso horário" htmlFor="st-fuso">
              <Select
                id="st-fuso"
                options={[
                { value: 'cat', label: 'Maputo (CAT, UTC+2)' },
                { value: 'utc', label: 'UTC' }]
                } />
              
            </FieldGroup>
            {role !== 'student' ?
            <FieldGroup label="Moeda" htmlFor="st-moeda">
                <Select
                id="st-moeda"
                options={[
                { value: 'mzn', label: 'Metical (MZN)' },
                { value: 'usd', label: 'Dólar (USD)' }]
                } />
              
              </FieldGroup> :
            null}
            <FieldGroup label="Nova palavra-passe" hint="Opcional" htmlFor="st-pass">
              <Input id="st-pass" type="password" placeholder="••••••••" />
            </FieldGroup>
          </div>
        </Section>

        <div className="space-y-3">
          <Section title="Aparência">
            <Segmented
              value={theme}
              onChange={(id) => {
                if (id !== theme) toggleTheme();
              }}
              items={[
              { id: 'light', label: 'Claro' },
              { id: 'dark', label: 'Escuro' }]
              } />
            
            <p className="mt-2 flex items-center gap-1.5 text-2xs text-fg-subtle">
              {theme === 'light' ? <SunIcon className="h-3 w-3" /> : <MoonIcon className="h-3 w-3" />}
              Tema {theme === 'light' ? 'claro' : 'escuro'}
            </p>
          </Section>

          <Section title="Notificações">
            <div className="space-y-2.5">
              <Checkbox label="Email" defaultChecked />
              <Checkbox label="SMS" defaultChecked />
              <Checkbox label="Resumo semanal" />
            </div>
          </Section>

          {role === 'admin' ?
          <Section title="Plataforma">
              <div className="space-y-3">
                <FieldGroup label="Comissão padrão" hint="%" htmlFor="st-com">
                  <Input id="st-com" type="number" defaultValue={30} className="tnum" />
                </FieldGroup>
                <Checkbox label="Aprovação manual de cursos" defaultChecked />
                <Checkbox label="Inscrições abertas" defaultChecked />
              </div>
            </Section> :
          null}
        </div>
      </div>
    </>);

}