import React from 'react';
import { AwardIcon, DownloadIcon, Share2Icon } from 'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { Button, IconButton } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { ProgressBar } from '../../components/ui/Bits';
import { certificates } from '../../data/certificates';
import { enrolledCourses } from '../../data/courses';

const mine = certificates.filter((item) => item.student === 'Délcio Nhaca');
const inProgress = enrolledCourses.filter((course) => course.progress >= 50);

export function StudentCertificates() {
  return (
    <>
      <PageHeader title="Certificados" />

      <div className="grid grid-cols-1 gap-3 xl:grid-cols-3">
        <Section className="xl:col-span-2" title="Emitidos" bodyClassName="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2">
          {mine.map((item) =>
          <article key={item.id} className="rounded-md border border-line p-3">
              <div className="flex items-start justify-between gap-2">
                <span className="flex h-8 w-8 items-center justify-center rounded-md border border-line bg-surface-2">
                  <AwardIcon className="h-4 w-4 text-accent" />
                </span>
                <span className="text-sm font-semibold text-fg tnum">{item.grade}%</span>
              </div>
              <h3 className="mt-2 text-sm font-medium text-fg">{item.course}</h3>
              <p className="mt-0.5 text-2xs text-fg-subtle tnum">
                {item.code} · {item.issued}
              </p>
              <div className="mt-3 flex items-center gap-1.5">
                <Button size="xs" variant="primary" icon={DownloadIcon}>
                  PDF
                </Button>
                <IconButton icon={Share2Icon} label={`Partilhar ${item.code}`} />
              </div>
            </article>
          )}
        </Section>

        <Section title="Em progresso" bodyClassName="p-0">
          <ul className="divide-y divide-line">
            {inProgress.map((course) =>
            <li key={course.id} className="px-4 py-3">
                <p className="truncate text-xs font-medium text-fg">{course.title}</p>
                <ProgressBar value={course.progress} className="mt-2" />
              </li>
            )}
          </ul>
        </Section>
      </div>
    </>);

}