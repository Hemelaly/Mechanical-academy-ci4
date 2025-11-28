<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

// Rotas do Shield (login, logout, etc.)
service('auth')->routes($routes);

$routes->get('reset-password',  'ResetPassword::showResetForm');
$routes->post('reset-password', 'ResetPassword::submitReset');

// Admin
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'role:admin'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/cursos', 'Dashboard::courses');
    $routes->get('dashboard/estudantes', 'Dashboard::students');
    $routes->get('dashboard/instrutores', 'Dashboard::instructors');
    $routes->get('dashboard/perfil', 'Dashboard::profile');
});

// Instructor
$routes->group('instructor', ['namespace' => 'App\Controllers\Instructor', 'filter' => 'role:instructor'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/meus_cursos', 'Dashboard::my_courses');
    $routes->get('dashboard/novo_curso', 'Dashboard::add_course');
    $routes->get('dashboard/jitsi', 'Dashboard::live');
    $routes->post('dashboard/jitsi/criar_sala', 'Dashboard::live');
    $routes->post('dashboard/jitsi/editar/(:num)', 'Dashboard::live/$1');   // se quiser reaproveitar o mesmo método
    $routes->get('dashboard/jitsi/deletar/(:num)', 'Dashboard::deleteJitsi/$1'); // ou outro método pra deletar
    $routes->get('dashboard/jitsi/stream/(:num)', 'Dashboard::stream/$1');
    $routes->post('dashboard/novo_curso/criar', 'CourseController::criar');
    $routes->get('dashboard/meus_cursos/editar/(:num)', 'Dashboard::edit_course/$1');
    $routes->post('dashboard/editar_curso/(:num)', 'CourseController::editar/$1');
    $routes->post('dashboard/meus_cursos/deletar/(:num)', 'CourseController::deletar/$1');
    $routes->get('dashboard/meus_estudantes', 'Dashboard::students');
    $routes->get('dashboard/financas', 'Dashboard::financial');
    $routes->get('dashboard/perfil', 'Dashboard::profile');
    $routes->post('dashboard/meus_estudantes/(:num)/(:num)', 'Dashboard::approveEnrollment/$1/$2');
});

// Student
$routes->group('student', ['namespace' => 'App\Controllers\Student', 'filter' => 'role:student'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/inscricoes', 'Dashboard::my_courses');
    $routes->get('dashboard/cursos', 'Dashboard::courses');
    $routes->get('dashboard/ver_aulas/(:num)', 'Dashboard::lessons/$1');
    $routes->get('dashboard/checkout/(:num)', 'Dashboard::checkout/$1');
    $routes->get('dashboard/perfil', 'Dashboard::profile');
    $routes->post('dashboard/perfil', 'Dashboard::profile');

    // Rotas para Marcar as aulas como completas
    $routes->group('lessons', function ($r) {
        $r->post('complete',   'LessonsController::complete');
        $r->post('uncomplete', 'LessonsController::uncomplete');
    });
});

// Rotas de Cursos
$routes->get('/courses/(:num)', 'PageController::excel/$1');

$routes->get('checkout/(:num)', 'PageController::index/$1');
$routes->post('checkout/pending/(:num)', 'Register::createPendingUser/$1');

// Pagamentos
$routes->post('mpesa/callback', 'MpesaCallback::stk');
$routes->post('mpesa/send', 'MpesaController::send');
$routes->get('payment/checkout/(:num)/(:num)', 'PaymentController::createPayment/$1/$2');
$routes->post('checkout/(:num)', 'PaymentController::createPayment/$1');
// $routes->post('student/payment/mpesa/(:num)', 'Student\PaymentController::mpesa/$1');
// $routes->post('pay/(:num)', 'Student\PaymentController::pay/$1');
// $routes->post('mpesa/webhook', 'MpesaWebhookController::receive');
