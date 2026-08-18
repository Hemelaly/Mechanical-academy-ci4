import React, { useMemo, useState } from 'react';
import { CheckIcon, DownloadIcon } from 'lucide-react';
import { PageHeader } from '../../components/shell/PageHeader';
import { Toolbar } from '../../components/dashboard/Toolbar';
import { CertificateStatusBadge } from '../../components/dashboard/status';
import { Button, IconButton } from '../../components/ui/Button';
import { Section } from '../../components/ui/Surface';
import { Table, TBody, TD, TH, THead, TR } from '../../components/ui/Table';
import { certificates } from '../../data/certificates';

const mine = certificates.filter((item) => item.instructor === 'Ana Chirindza' || item.course.includes('Excel'));

export function InstructorCertificates() {
  const [query, setQuery] = useState('');

  const filtered = useMemo(
    () =>
    mine.filter(
      (item) =>
      query.trim() === '' ||
      item.student.toLowerCase().includes(query.toLowerCase()) ||
      item.code.toLowerCase().includes(query.toLowerCase())
    ),
    [query]
  );

  return (
    <>
      <PageHeader
        title="Certificados"
        actions={
        <Button size="sm" variant="primary" icon={CheckIcon}>
            Emitir pendentes
          </Button>
        } />
      

      <Section bodyClassName="p-0">
        <Toolbar placeholder="Aluno ou código" query={query} onQueryChange={setQuery} />
        <Table>
          <THead>
            <TR hoverable={false}>
              <TH>Aluno</TH>
              <TH>Curso</TH>
              <TH align="right">Nota</TH>
              <TH>Estado</TH>
              <TH align="right">Emissão</TH>
              <TH align="right" />
            </TR>
          </THead>
          <TBody>
            {filtered.map((item) =>
            <TR key={item.id}>
                <TD className="font-medium">{item.student}</TD>
                <TD className="max-w-[220px] truncate text-fg-muted">{item.course}</TD>
                <TD align="right" className="tnum">
                  {item.grade}%
                </TD>
                <TD>
                  <CertificateStatusBadge status={item.status} />
                </TD>
                <TD align="right" className="text-2xs text-fg-subtle">
                  {item.issued}
                </TD>
                <TD align="right">
                  <IconButton icon={DownloadIcon} label={`Descarregar certificado de ${item.student}`} />
                </TD>
              </TR>
            )}
            {filtered.length === 0 ?
            <TR hoverable={false}>
                <TD colSpan={6} className="py-10 text-center text-xs text-fg-muted">
                  Nenhum certificado encontrado.
                </TD>
              </TR> :
            null}
          </TBody>
        </Table>
      </Section>
    </>);

}