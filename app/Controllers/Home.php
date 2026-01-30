<?php

namespace App\Controllers;

use App\Models\CourseModel;

class Home extends BaseController
{
  public function index(): string
  {
    $courseModel = new CourseModel();
    $user = service('auth')->user();

    $course = $courseModel->where('status_course', 'Ativo')->findAll();

    return view('home', [
      'courses' => $course,
      'user' => $user,
    ]);
  }
}
