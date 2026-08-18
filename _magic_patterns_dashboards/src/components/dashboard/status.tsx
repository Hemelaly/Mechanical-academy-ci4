import React from 'react';
import { Badge, type BadgeTone } from '../ui/Badge';
import type { CourseStatus } from '../../data/courses';
import type { PaymentStatus } from '../../data/finance';
import type { PersonStatus } from '../../data/people';
import type { CertificateStatus } from '../../data/certificates';

const courseStatusMap: Record<CourseStatus, {label: string;tone: BadgeTone;}> = {
  publicado: { label: 'Publicado', tone: 'success' },
  rascunho: { label: 'Rascunho', tone: 'neutral' },
  revisao: { label: 'Em revisão', tone: 'warn' },
  arquivado: { label: 'Arquivado', tone: 'neutral' }
};

const paymentStatusMap: Record<PaymentStatus, {label: string;tone: BadgeTone;}> = {
  pago: { label: 'Pago', tone: 'success' },
  pendente: { label: 'Pendente', tone: 'warn' },
  falhado: { label: 'Falhado', tone: 'danger' },
  reembolsado: { label: 'Reembolsado', tone: 'info' }
};

export function CourseStatusBadge({ status }: {status: CourseStatus;}) {
  const item = courseStatusMap[status];
  return (
    <Badge tone={item.tone} dot>
      {item.label}
    </Badge>);

}

export function PaymentStatusBadge({ status }: {status: PaymentStatus;}) {
  const item = paymentStatusMap[status];
  return (
    <Badge tone={item.tone} dot>
      {item.label}
    </Badge>);

}

const personStatusMap: Record<PersonStatus, {label: string;tone: BadgeTone;}> = {
  activo: { label: 'Activo', tone: 'success' },
  inactivo: { label: 'Inactivo', tone: 'neutral' },
  suspenso: { label: 'Suspenso', tone: 'danger' },
  pendente: { label: 'Pendente', tone: 'warn' }
};

export function PersonStatusBadge({ status }: {status: PersonStatus;}) {
  const item = personStatusMap[status];
  return (
    <Badge tone={item.tone} dot>
      {item.label}
    </Badge>);

}

const certificateStatusMap: Record<CertificateStatus, {label: string;tone: BadgeTone;}> = {
  emitido: { label: 'Emitido', tone: 'success' },
  pendente: { label: 'Pendente', tone: 'warn' },
  revogado: { label: 'Revogado', tone: 'danger' }
};

export function CertificateStatusBadge({ status }: {status: CertificateStatus;}) {
  const item = certificateStatusMap[status];
  return (
    <Badge tone={item.tone} dot>
      {item.label}
    </Badge>);

}