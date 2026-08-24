<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Services\CourseCommerceService;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Academy;
use Config\Services;

class Home extends BaseController
{
  public function index(): string
  {
    $courseModel = new CourseModel();
    $commerce = new CourseCommerceService();
    $user = service('auth')->user();

    $courses = $courseModel->where('status_course', 'Ativo')->findAll();

    $minutesByCourseId = [];
    $durationRows = \Config\Database::connect()
      ->table('modules m')
      ->select('m.id_course_module, COALESCE(SUM(l.duration_lesson), 0) AS total_minutes', false)
      ->join('lessons l', 'l.id_module_lesson = m.id_module', 'left')
      ->groupBy('m.id_course_module')
      ->get()
      ->getResultArray();

    foreach ($durationRows as $row) {
      $minutesByCourseId[(int) $row['id_course_module']] = (int) $row['total_minutes'];
    }

    foreach ($courses as $course) {
      $totalMinutes = $minutesByCourseId[(int) $course->id_course] ?? 0;
      $course->total_minutes_course = $totalMinutes;
      $course->total_hours_label = $commerce->resolveHoursLabel($course, $totalMinutes);
      $course->list_price = $commerce->getListPrice($course);
      $course->promo_price = $commerce->getPromoPrice($course);
      $course->effective_price = $commerce->getEffectivePrice($course);
      $course->has_promo = $commerce->hasPromo($course);
      $course->discount_percent = $commerce->getDiscountPercent($course);
      $course->promo_ends_at = $commerce->getPromoEndsAt($course);
      $course->promo_remaining_seconds = $commerce->getPromoRemainingSeconds($course);
      $course->free_lessons = (int) ($course->free_lessons_count_course ?? $course->free_lessons_course ?? 0);
    }

    // Melhor promoção activa para o painel lateral (maior %, depois a que termina primeiro).
    $promoFeatured = null;
    $enrolledCourseIds = [];
    if ($user) {
      $enrolledCourseIds = array_map(
        'intval',
        array_column(
          db_connect()->table('enrollments')
            ->select('id_course_enrollment')
            ->where('id_student_enrollment', (int) $user->id)
            ->whereIn('status_enrollment', ['ativa', 'pendente'])
            ->get()
            ->getResultArray(),
          'id_course_enrollment'
        )
      );
    }

    foreach ($courses as $course) {
      if (empty($course->has_promo)) {
        continue;
      }
      // Sem data de fim: promo válida; com data: só se ainda houver tempo.
      $remaining = (int) ($course->promo_remaining_seconds ?? 0);
      $endsAt = $course->promo_ends_at ?? null;
      if ($endsAt !== null && $endsAt !== '' && $remaining <= 0) {
        continue;
      }
      if (in_array((int) $course->id_course, $enrolledCourseIds, true)) {
        continue;
      }

      if ($promoFeatured === null) {
        $promoFeatured = $course;
        continue;
      }

      $bestPct = (int) ($promoFeatured->discount_percent ?? 0);
      $candPct = (int) ($course->discount_percent ?? 0);
      if ($candPct > $bestPct) {
        $promoFeatured = $course;
        continue;
      }
      if ($candPct < $bestPct) {
        continue;
      }

      $bestLeft = (int) ($promoFeatured->promo_remaining_seconds ?? 0);
      $candLeft = (int) ($course->promo_remaining_seconds ?? 0);
      // 0 = sem deadline → deixa para o fim; preferir a que acaba mais cedo.
      if ($candLeft > 0 && ($bestLeft <= 0 || $candLeft < $bestLeft)) {
        $promoFeatured = $course;
      }
    }

    return view('home', [
      'courses' => $courses,
      'user' => $user,
      'promoFeatured' => $promoFeatured,
    ]);
  }

  /**
   * Subscrição da newsletter — notifica academy@ (contacto com clientes).
   */
  public function subscribe(): ResponseInterface
  {
    $cf = new \App\Libraries\CloudflareTurnstile();
    $cfCheck = $cf->verifyRequest($this->request);
    if (! $cfCheck['ok']) {
      return $this->response->setStatusCode(422)->setJSON([
        'ok' => false,
        'message' => $cfCheck['message'] ?? 'Verificação Cloudflare falhou.',
      ]);
    }

    $email = strtolower(trim((string) $this->request->getPost('email')));

    if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return $this->response->setStatusCode(422)->setJSON([
        'ok' => false,
        'message' => 'Indique um email válido.',
      ]);
    }

    $academy = config(Academy::class);
    $to = trim((string) ($academy->contactEmail ?? ''));
    if ($to === '') {
      $to = 'academy@mechanical.co.mz';
    }

    $safeEmail = esc($email);
    $when = date('d/m/Y H:i');

    try {
      $mail = Services::email();
      $mail->setTo($to);
      $mail->setReplyTo($email);
      $mail->setSubject('Nova subscrição — Newsletter Academy');
      $mail->setMessage(\App\Libraries\BrandEmail::render([
        'preheader' => 'Nova subscrição na newsletter: ' . $email,
        'eyebrow'   => 'Newsletter',
        'title'     => 'Nova subscrição à newsletter',
        'body'      => \App\Libraries\BrandEmail::p(
          'Um visitante pediu para ser notificado sobre novos cursos.'
        ),
        'info' => [
          ['label' => 'Email', 'value' => $safeEmail],
          ['label' => 'Recebido em', 'value' => esc($when)],
        ],
      ]));

      if (! $mail->send()) {
        log_message('error', 'Newsletter subscribe SMTP fail: {debug}', [
          'debug' => $mail->printDebugger(['headers']),
        ]);

        return $this->response->setStatusCode(500)->setJSON([
          'ok' => false,
          'message' => 'Não foi possível registar a subscrição. Tente novamente.',
        ]);
      }
    } catch (\Throwable $e) {
      log_message('error', 'Newsletter subscribe error: {msg}', ['msg' => $e->getMessage()]);

      return $this->response->setStatusCode(500)->setJSON([
        'ok' => false,
        'message' => 'Não foi possível registar a subscrição. Tente novamente.',
      ]);
    }

    return $this->response->setJSON([
      'ok' => true,
      'message' => 'Obrigado! Vamos notificá-lo sobre novos cursos.',
    ]);
  }
}
