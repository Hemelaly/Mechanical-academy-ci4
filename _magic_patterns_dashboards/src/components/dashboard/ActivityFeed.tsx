import React from 'react';
import { cn } from '../../utils/cn';
import type { ActivityItem, ActivityTone } from '../../data/activity';

const toneDots: Record<ActivityTone, string> = {
  accent: 'bg-accent',
  success: 'bg-success',
  warn: 'bg-warn',
  info: 'bg-info'
};

export function ActivityFeed({ items }: {items: ActivityItem[];}) {
  return (
    <ul className="divide-y divide-line">
      {items.map((item) =>
      <li key={item.id} className="flex items-start gap-2.5 px-4 py-2.5">
          <span
          aria-hidden="true"
          className={cn('mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full', toneDots[item.tone])} />
        
          <p className="min-w-0 flex-1 text-xs leading-5 text-fg-muted">
            <span className="font-medium text-fg">{item.actor}</span> {item.action}{' '}
            <span className="font-medium text-fg">{item.target}</span>
          </p>
          <span className="shrink-0 text-2xs text-fg-subtle">{item.time}</span>
        </li>
      )}
    </ul>);

}