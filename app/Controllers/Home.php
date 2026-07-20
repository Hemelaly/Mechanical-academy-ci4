<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Services\CourseCommerceService;

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

    return view('home', [
      'courses' => $courses,
      'user' => $user,
    ]);
  }
}
