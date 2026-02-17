<?php

namespace App\Controllers;

use App\Models\CourseModel;

class Home extends BaseController
{
  private function formatCourseHours(int $totalMinutes): string
  {
    if ($totalMinutes <= 0) {
      return '0 Horas';
    }

    $hours = $totalMinutes / 60;
    $formattedHours = fmod($hours, 1.0) === 0.0
      ? number_format($hours, 0, ',', '.')
      : number_format($hours, 1, ',', '.');

    $unit = abs($hours - 1.0) < 0.00001 ? 'Hora' : 'Horas';

    return $formattedHours . ' ' . $unit;
  }

  public function index(): string
  {
    $courseModel = new CourseModel();
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
      $course->total_hours_label = $this->formatCourseHours($totalMinutes);
    }

    return view('home', [
      'courses' => $courses,
      'user' => $user,
    ]);
  }
}
