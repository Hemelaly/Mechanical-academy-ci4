import React from 'react';
import { SaveIcon } from 'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { Button } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { Avatar } from '../../components/ui/Bits';
import { Checkbox, FieldGroup, Input, Select } from '../../components/ui/Field';

export function StudentProfile() {
  return (
    <>
      <PageHeader
        title="Perfil"
        actions={
        <Button size="sm" variant="primary" icon={SaveIcon}>
            Guardar
          </Button>
        } />
      

      <div className="grid grid-cols-1 gap-3 xl:grid-cols-3">
        <Section className="xl:col-span-2" title="Dados pessoais">
          <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
            <FieldGroup label="Nome completo" required htmlFor="pf-nome">
              <Input id="pf-nome" defaultValue="Délcio Nhaca" />
            </FieldGroup>
            <FieldGroup label="Email" required htmlFor="pf-email">
              <Input id="pf-email" type="email" defaultValue="delcio.nhaca@gmail.com" />
            </FieldGroup>
            <FieldGroup label="Telefone (M-Pesa)" htmlFor="pf-tel">
              <Input id="pf-tel" defaultValue="+258 84 512 8830" className="tnum" />
            </FieldGroup>
            <FieldGroup label="Província" htmlFor="pf-prov">
              <Select
                id="pf-prov"
                options={[
                { value: 'maputo', label: 'Maputo' },
                { value: 'sofala', label: 'Sofala' },
                { value: 'nampula', label: 'Nampula' },
                { value: 'tete', label: 'Tete' }]
                } />
              
            </FieldGroup>
            <FieldGroup label="Idioma" htmlFor="pf-idioma">
              <Select
                id="pf-idioma"
                options={[
                { value: 'pt', label: 'Português' },
                { value: 'en', label: 'Inglês' }]
                } />
              
            </FieldGroup>
            <FieldGroup label="Nova palavra-passe" hint="Opcional" htmlFor="pf-pass">
              <Input id="pf-pass" type="password" placeholder="••••••••" />
            </FieldGroup>
          </div>
        </Section>

        <div className="space-y-3">
          <Section title="Fotografia">
            <div className="flex items-center gap-3">
              <Avatar name="Délcio Nhaca" size="lg" />
              <div className="flex flex-col gap-1.5">
                <Button size="xs">Carregar</Button>
                <Button size="xs" variant="ghost">
                  Remover
                </Button>
              </div>
            </div>
          </Section>

          <Section title="Notificações">
            <div className="space-y-2.5">
              <Checkbox label="Email" description="Novas aulas e certificados" defaultChecked />
              <Checkbox label="SMS" description="Aulas ao vivo e pagamentos" defaultChecked />
              <Checkbox label="Resumo semanal" />
            </div>
          </Section>
        </div>
      </div>
    </>);

}