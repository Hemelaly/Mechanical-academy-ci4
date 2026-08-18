import React, { useState } from 'react';
import { ChevronDownIcon, MailIcon, MessageSquareIcon, PhoneIcon, SendIcon } from 'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { Button } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { FieldGroup, Input, Select, Textarea } from '../../components/ui/Field';
import { cn } from '../../utils/cn';

const faqs = [
{
  id: 'f1',
  question: 'Como pago um curso por M-Pesa?',
  answer: 'Escolha M-Pesa no checkout, introduza o número e confirme o pedido no telemóvel.'
},
{
  id: 'f2',
  question: 'Quando recebo o certificado?',
  answer: 'Assim que concluir 100% do curso, o certificado é emitido automaticamente.'
},
{
  id: 'f3',
  question: 'As aulas ao vivo ficam gravadas?',
  answer: 'Sim, a gravação fica disponível até 24 h depois da sessão.'
},
{
  id: 'f4',
  question: 'Posso pedir reembolso?',
  answer: 'Sim, até 7 dias após a compra e com menos de 20% do curso concluído.'
}];


const channels = [
{ id: 'c1', icon: MessageSquareIcon, label: 'WhatsApp', value: '+258 84 000 1234' },
{ id: 'c2', icon: PhoneIcon, label: 'Telefone', value: '+258 21 300 500' },
{ id: 'c3', icon: MailIcon, label: 'Email', value: 'suporte@academy.co.mz' }];


export function Support() {
  const [open, setOpen] = useState<string | null>('f1');

  return (
    <>
      <PageHeader title="Suporte" />

      <div className="grid grid-cols-1 gap-3 xl:grid-cols-3">
        <Section className="xl:col-span-2" title="Perguntas frequentes" bodyClassName="p-0">
          <ul className="divide-y divide-line">
            {faqs.map((faq) => {
              const expanded = open === faq.id;
              return (
                <li key={faq.id}>
                  <button
                    type="button"
                    aria-expanded={expanded}
                    onClick={() => setOpen(expanded ? null : faq.id)}
                    className="flex w-full items-center justify-between gap-3 px-4 py-2.5 text-left transition-colors duration-100 ease-out hover:bg-surface-2">
                    
                    <span className="text-sm font-medium text-fg">{faq.question}</span>
                    <ChevronDownIcon
                      className={cn(
                        'h-3.5 w-3.5 shrink-0 text-fg-subtle transition-transform duration-150 ease-out',
                        expanded && 'rotate-180'
                      )} />
                    
                  </button>
                  {expanded ? <p className="px-4 pb-3 text-xs leading-5 text-fg-muted">{faq.answer}</p> : null}
                </li>);

            })}
          </ul>
        </Section>

        <div className="space-y-3">
          <Section title="Contactos" bodyClassName="p-0">
            <ul className="divide-y divide-line">
              {channels.map((channel) =>
              <li key={channel.id} className="flex items-center gap-2.5 px-4 py-2.5">
                  <channel.icon className="h-4 w-4 shrink-0 text-fg-subtle" />
                  <div className="min-w-0 flex-1">
                    <p className="text-xs font-medium text-fg">{channel.label}</p>
                    <p className="truncate text-2xs text-fg-subtle tnum">{channel.value}</p>
                  </div>
                </li>
              )}
            </ul>
          </Section>

          <Section title="Abrir pedido">
            <form className="space-y-3" onSubmit={(event) => event.preventDefault()}>
              <FieldGroup label="Assunto" htmlFor="sp-assunto">
                <Select
                  id="sp-assunto"
                  options={[
                  { value: 'pagamento', label: 'Pagamento' },
                  { value: 'acesso', label: 'Acesso ao curso' },
                  { value: 'certificado', label: 'Certificado' },
                  { value: 'outro', label: 'Outro' }]
                  } />
                
              </FieldGroup>
              <FieldGroup label="Referência" hint="Opcional" htmlFor="sp-ref">
                <Input id="sp-ref" placeholder="MPS-84213" className="tnum" />
              </FieldGroup>
              <FieldGroup label="Descrição" htmlFor="sp-desc">
                <Textarea id="sp-desc" rows={4} placeholder="Descreva o problema" />
              </FieldGroup>
              <Button variant="primary" size="sm" block icon={SendIcon} type="submit">
                Enviar pedido
              </Button>
            </form>
          </Section>
        </div>
      </div>
    </>);

}