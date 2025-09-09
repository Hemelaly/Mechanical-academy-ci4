<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

// Rotas do Shield (login, logout, etc.)
service('auth')->routes($routes);

// Admin
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
});

// Instructor
$routes->group('instructor', ['namespace' => 'App\Controllers\Instructor', 'filter' => 'role:instructor'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/meus_cursos', 'Dashboard::my_courses');
    $routes->get('dashboard/novo_curso', 'Dashboard::add_course');
    $routes->post('dashboard/novo_curso/criar', 'CourseController::criar');
    $routes->get('dashboard/meus_estudantes', 'Dashboard::students');
    $routes->get('dashboard/financas', 'Dashboard::financial');
    $routes->get('dashboard/perfil', 'Dashboard::profile');
});

// Student
$routes->group('student', ['namespace' => 'App\Controllers\Student'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/meus_cursos', 'Dashboard::my_courses');
    $routes->get('dashboard/cursos', 'Dashboard::courses');
    $routes->get('dashboard/ver_aulas/(:num)', 'Dashboard::lessons/$1');
    $routes->get('dashboard/perfil', 'Dashboard::profile');
});


