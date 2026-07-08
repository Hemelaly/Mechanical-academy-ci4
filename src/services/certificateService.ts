import { api } from './api';

export type GenerateCertificatePayload = {
  name_student_certificate: string;
  name_course_certificate: string;
  concluded_at_certificate: string;
  trainer_name_certificate?: string;
  director_name_certificate?: string;
};

export type CertificateVerificationResponse = {
  status: number;
  valid: boolean;
  message: string;
  data?: {
    code_certificate: string;
    name_student_certificate: string;
    name_course_certificate: string;
    concluded_at_certificate: string;
    trainer_name_certificate?: string | null;
    director_name_certificate?: string | null;
    verification_url_certificate: string;
    download_url_certificate: string;
  };
};

export async function generateCertificate(payload: GenerateCertificatePayload) {
  const response = await api.post('/certificates/generate', payload);
  return response.data;
}

export async function verifyCertificate(code: string): Promise<CertificateVerificationResponse> {
  const response = await api.get(`/certificates/verify/${encodeURIComponent(code)}`);
  return response.data;
}
