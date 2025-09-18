<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

// Rotas do Shield (login, logout, etc.)
service('auth')->routes($routes);

$routes->get('register', 'Register::showForm'); // se quiser mostrar o form
$routes->post('register', 'Register::register');

// Admin
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'role:admin'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
});

// Instructor
$routes->group('instructor', ['namespace' => 'App\Controllers\Instructor', 'filter' => 'role:instructor'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/meus_cursos', 'Dashboard::my_courses');
    $routes->get('dashboard/novo_curso', 'Dashboard::add_course');
    $routes->post('dashboard/novo_curso/criar', 'CourseController::criar');
    $routes->get('dashboard/meus_cursos/editar/(:num)', 'Dashboard::edit_course/$1');
    $routes->post('dashboard/editar_curso/(:num)', 'CourseController::editar/$1');
    $routes->post('dashboard/meus_cursos/deletar/(:num)', 'CourseController::deletar/$1');
    $routes->get('dashboard/meus_estudantes', 'Dashboard::students');
    $routes->get('dashboard/financas', 'Dashboard::financial');
    $routes->get('dashboard/perfil', 'Dashboard::profile');
    $routes->post('dashboard/meus_estudantes/(:num)', 'Dashboard::updateEnrollment/$1');

});

// Student
$routes->group('student', ['namespace' => 'App\Controllers\Student', 'filter' => 'role:student'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/meus_cursos', 'Dashboard::my_courses');
    $routes->get('dashboard/cursos', 'Dashboard::courses');
    $routes->get('dashboard/ver_aulas/(:num)', 'Dashboard::lessons/$1');
    $routes->get('dashboard/checkout/(:num)', 'Dashboard::checkout/$1');
    $routes->get('dashboard/perfil', 'Dashboard::profile');
});

// Pagamentos
$routes->post('checkout/(:num)', 'PaymentController::createPayment/$1');
// $routes->post('student/payment/mpesa/(:num)', 'Student\PaymentController::mpesa/$1');
// $routes->post('pay/(:num)', 'Student\PaymentController::pay/$1');
// $routes->post('mpesa/webhook', 'MpesaWebhookController::receive');

