import { FormEvent, useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import {
  CertificateVerificationResponse,
  verifyCertificate,
} from '../../services/certificateService';

export default function VerifyCertificatePage() {
  const { code } = useParams();
  const navigate = useNavigate();

  const [certificateCode, setCertificateCode] = useState(code ?? '');
  const [loading, setLoading] = useState(false);
  const [result, setResult] = useState<CertificateVerificationResponse | null>(null);
  const [error, setError] = useState('');

  async function handleVerify(selectedCode: string) {
    const cleanCode = selectedCode.trim();

    if (!cleanCode) {
      setError('Informe o código do certificado.');
      return;
    }

    try {
      setLoading(true);
      setError('');
      setResult(null);

      const data = await verifyCertificate(cleanCode);
      setResult(data);
    } catch {
      setResult({
        status: 404,
        valid: false,
        message: 'Certificado não encontrado ou inválido.',
      });
    } finally {
      setLoading(false);
    }
  }

  function handleSubmit(event: FormEvent) {
    event.preventDefault();

    const cleanCode = certificateCode.trim();

    if (cleanCode) {
      navigate(`/certificados/verificar/${cleanCode}`);
      handleVerify(cleanCode);
    }
  }

  useEffect(() => {
    if (code) {
      setCertificateCode(code);
      handleVerify(code);
    }
  }, [code]);

  return (
    <main className="min-h-screen bg-slate-50 px-4 py-8">
      <section className="mx-auto max-w-2xl rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
        <div className="mb-6 text-center">
          <p className="text-xs font-semibold uppercase tracking-[0.22em] text-blue-900">
            Mechanical Academy
          </p>

          <h1 className="mt-2 text-2xl font-bold text-slate-900">
            Verificação de Certificado
          </h1>

          <p className="mt-2 text-sm text-slate-600">
            Confirme a autenticidade de um certificado emitido pela Mechanical Academy.
          </p>
        </div>

        <form onSubmit={handleSubmit} className="space-y-3">
          <label className="block text-sm font-medium text-slate-700">
            Código do certificado
          </label>

          <div className="flex flex-col gap-3 sm:flex-row">
            <input
              value={certificateCode}
              onChange={(event) => setCertificateCode(event.target.value)}
              placeholder="Ex: MTA-2026-A1B2C3D4"
              className="min-h-11 flex-1 rounded-xl border border-slate-300 px-4 text-sm outline-none focus:border-blue-700 focus:ring-2 focus:ring-blue-100"
            />

            <button
              type="submit"
              disabled={loading}
              className="min-h-11 rounded-xl bg-blue-900 px-5 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-60"
            >
              {loading ? 'Verificando...' : 'Verificar'}
            </button>
          </div>

          {error && <p className="text-sm text-red-600">{error}</p>}
        </form>

        {result && (
          <div
            className={[
              'mt-6 rounded-2xl border p-4',
              result.valid ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50',
            ].join(' ')}
          >
            <div className="flex items-start gap-3">
              <div
                className={[
                  'flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-lg font-bold',
                  result.valid ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white',
                ].join(' ')}
              >
                {result.valid ? '✓' : '!'}
              </div>

              <div className="min-w-0 flex-1">
                <h2
                  className={[
                    'text-base font-bold',
                    result.valid ? 'text-emerald-900' : 'text-red-900',
                  ].join(' ')}
                >
                  {result.message}
                </h2>

                {result.valid && result.data && (
                  <div className="mt-4 space-y-2 text-sm text-slate-700">
                    <p>
                      <span className="font-semibold">Formando:</span>{' '}
                      {result.data.name_student_certificate}
                    </p>

                    <p>
                      <span className="font-semibold">Curso:</span>{' '}
                      {result.data.name_course_certificate}
                    </p>

                    <p>
                      <span className="font-semibold">Conclusão:</span>{' '}
                      {new Date(result.data.concluded_at_certificate).toLocaleDateString('pt-MZ')}
                    </p>

                    <p>
                      <span className="font-semibold">Código:</span>{' '}
                      {result.data.code_certificate}
                    </p>

                    <a
                      href={result.data.download_url_certificate}
                      target="_blank"
                      rel="noreferrer"
                      className="mt-4 inline-flex min-h-10 items-center rounded-xl bg-blue-900 px-4 text-sm font-semibold text-white"
                    >
                      Baixar certificado
                    </a>
                  </div>
                )}
              </div>
            </div>
          </div>
        )}
      </section>
    </main>
  );
}
