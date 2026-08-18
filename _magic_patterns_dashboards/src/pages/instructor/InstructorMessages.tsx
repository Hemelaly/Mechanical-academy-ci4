import React, { useState } from 'react';
import { SearchIcon, SendIcon } from 'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { Button } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { Avatar } from '../../components/ui/Bits';
import { Input } from '../../components/ui/Field';
import { cn } from '../../utils/cn';
import { conversation, messageThreads } from '../../data/messages';

export function InstructorMessages() {
  const [activeId, setActiveId] = useState(messageThreads[0].id);
  const active = messageThreads.find((thread) => thread.id === activeId) ?? messageThreads[0];

  return (
    <>
      <PageHeader title="Mensagens" />

      <div className="grid grid-cols-1 gap-3 lg:grid-cols-[300px_1fr]">
        <Section bodyClassName="p-0">
          <div className="border-b border-line p-2">
            <Input icon={SearchIcon} placeholder="Pesquisar" aria-label="Pesquisar conversas" className="h-8 text-xs" />
          </div>
          <ul className="divide-y divide-line">
            {messageThreads.map((thread) =>
            <li key={thread.id}>
                <button
                type="button"
                onClick={() => setActiveId(thread.id)}
                className={cn(
                  'flex w-full items-start gap-2.5 px-3 py-2.5 text-left transition-colors duration-100 ease-out',
                  thread.id === activeId ? 'bg-accent-soft' : 'hover:bg-surface-2'
                )}>
                
                  <Avatar name={thread.student} size="sm" />
                  <span className="min-w-0 flex-1">
                    <span className="flex items-center justify-between gap-2">
                      <span className="truncate text-xs font-medium text-fg">{thread.student}</span>
                      <span className="shrink-0 text-2xs text-fg-subtle tnum">{thread.time}</span>
                    </span>
                    <span className="mt-0.5 block truncate text-2xs text-fg-muted">{thread.preview}</span>
                  </span>
                  {thread.unread ?
                <span aria-label="Não lida" className="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-accent" /> :
                null}
                </button>
              </li>
            )}
          </ul>
        </Section>

        <Section title={active.student} description={active.course} bodyClassName="p-0">
          <ul className="space-y-3 p-4">
            {conversation.map((message) =>
            <li
              key={message.id}
              className={cn('flex', message.from === 'formador' ? 'justify-end' : 'justify-start')}>
              
                <div
                className={cn(
                  'max-w-[75%] rounded-md border px-3 py-2',
                  message.from === 'formador' ?
                  'border-transparent bg-accent text-accent-fg' :
                  'border-line bg-surface-2 text-fg'
                )}>
                
                  <p className="text-xs leading-5">{message.body}</p>
                  <p
                  className={cn(
                    'mt-1 text-2xs tnum',
                    message.from === 'formador' ? 'text-accent-fg opacity-70' : 'text-fg-subtle'
                  )}>
                  
                    {message.time}
                  </p>
                </div>
              </li>
            )}
          </ul>

          <form className="flex items-center gap-2 border-t border-line p-2" onSubmit={(event) => event.preventDefault()}>
            <Input placeholder="Escrever mensagem…" aria-label="Escrever mensagem" />
            <Button variant="primary" icon={SendIcon} type="submit">
              Enviar
            </Button>
          </form>
        </Section>
      </div>
    </>);

}