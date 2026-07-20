<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

// Rotas do Shield (login, logout, etc.)
service('auth')->routes($routes, ['except' => ['register']]);
$routes->get('novo_usuario', '\CodeIgniter\Shield\Controllers\RegisterController::registerView', ['as' => 'register', 'filter' => 'role:admin']);
$routes->post('novo_usuario', '\CodeIgniter\Shield\Controllers\RegisterController::registerAction', ['filter' => 'role:admin']);

$routes->get('reset-password',  'ResetPassword::showResetForm');
$routes->post('reset-password', 'ResetPassword::submitReset');
$routes->post('reset-password/request', 'ResetPassword::requestReset');

// Admin
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'role:admin'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/cursos', 'Dashboard::courses');
    $routes->get('dashboard/cursos/export', 'Dashboard::exportCoursesCsv');
    $routes->post('dashboard/cursos/toggle-status', 'Dashboard::toggleCourseStatus');
    $routes->get('dashboard/notificacoes', 'Dashboard::notifications');
    $routes->get('dashboard/notifications/data', 'Dashboard::notificationsData');
    $routes->get('dashboard/estudantes', 'Dashboard::students');
    $routes->get('dashboard/estudantes/data', 'Dashboard::studentsData');
    $routes->get('dashboard/estudantes/search', 'Dashboard::studentsSearch');
    $routes->post('dashboard/estudantes/matricular', 'Dashboard::manualEnroll');
    $routes->get('dashboard/instrutores', 'Dashboard::instructors');
    $routes->get('dashboard/instrutores/data', 'Dashboard::instructorsData');
    $routes->post('dashboard/usuarios/toggle', 'Dashboard::toggleUserStatus');
    $routes->post('dashboard/usuarios/delete', 'Dashboard::deleteUser');
    $routes->post('dashboard/usuarios/message', 'Dashboard::sendUserMessage');
    $routes->post('dashboard/usuarios/create', 'Dashboard::createUser');
    $routes->post('dashboard/usuarios/update', 'Dashboard::updateUser');
    $routes->get('dashboard/financas', 'Dashboard::financial');
    $routes->get('dashboard/financas/data', 'Dashboard::financialData');
    $routes->get('dashboard/perfil', 'Dashboard::profile');
    $routes->post('dashboard/perfil', 'Dashboard::profile');
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
    $routes->post('dashboard/jitsi/deletar/(:num)', 'Dashboard::deleteJitsi/$1');
    $routes->post('dashboard/jitsi/stream/(:num)/end', 'Dashboard::endStream/$1');
    $routes->post('dashboard/jitsi/stream/(:num)/recording', 'Dashboard::storeRecording/$1');
    $routes->post('dashboard/jitsi/recordings/(:num)/publish', 'Dashboard::toggleRecordingPublish/$1');
    $routes->post('dashboard/novo_curso/criar', 'CourseController::criar');
    $routes->post('dashboard/novo_curso/rascunho', 'CourseController::draftCreate');
    $routes->post('dashboard/novo_curso/rascunho/(:num)', 'CourseController::draftSave/$1');
    $routes->get('dashboard/meus_cursos/editar/(:num)', 'Dashboard::edit_course/$1');
    $routes->get('dashboard/cursos/preview/(:num)', 'Dashboard::course_preview/$1');
    $routes->post('dashboard/editar_curso/(:num)', 'CourseController::editar/$1');
    $routes->post('dashboard/meus_cursos/deletar/(:num)', 'CourseController::deletar/$1');
    $routes->get('dashboard/meus_estudantes', 'Dashboard::students');
    $routes->get('dashboard/meus_estudantes/data', 'Dashboard::studentsData');
    $routes->get('dashboard/meus_estudantes/pending', 'Dashboard::pendingPaymentsData');
    $routes->post('dashboard/meus_estudantes/toggle/(:num)', 'Dashboard::toggleEnrollment/$1');
    $routes->post('dashboard/meus_estudantes/matricular', 'Dashboard::manualEnroll');
    $routes->post('dashboard/meus_estudantes/demo', 'Dashboard::grantDemoAccess');
    $routes->get('dashboard/financas', 'Dashboard::financial');
    $routes->get('dashboard/financas/data', 'Dashboard::financialData');
    $routes->get('dashboard/logs', 'Dashboard::logs');
    $routes->get('dashboard/logs/data', 'Dashboard::logsData');
    $routes->get('dashboard/logs/export', 'Dashboard::logsExportCsv');
    $routes->get('dashboard/perfil', 'Dashboard::profile');
    $routes->post('dashboard/perfil', 'Dashboard::profile');
    $routes->get('dashboard/certificados', 'Dashboard::certificate');
    $routes->post('dashboard/certificados', '\App\Controllers\Certificates::upload');
    $routes->post('dashboard/certificados/delete', '\App\Controllers\Certificates::deleteCertificate');
    $routes->post('dashboard/meus_estudantes/(:num)/(:num)', 'Dashboard::approveEnrollment/$1/$2');
});

// Student
$routes->group('student', ['namespace' => 'App\Controllers\Student', 'filter' => 'role:student'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/inscricoes', 'Dashboard::my_courses');
    $routes->get('dashboard/inscricoes/(:segment)/(:segment)', 'Dashboard::lessonsBySlug/$1/$2');
    $routes->get('dashboard/cursos', 'Dashboard::courses');
    $routes->get('dashboard/aulas_ao_vivo', 'Dashboard::liveClasses');
    $routes->get('dashboard/aulas_ao_vivo/stream/(:num)', 'Dashboard::liveStream/$1');
    $routes->get('dashboard/ver_aulas/(:num)', 'Dashboard::lessons/$1');
    $routes->get('dashboard/checkout/(:num)', 'Dashboard::checkout/$1');
    $routes->get('dashboard/perfil', 'Dashboard::profile');
    $routes->post('dashboard/perfil', 'Dashboard::profile');
    $routes->get('dashboard/certificados', 'Dashboard::certificate');
    $routes->post('certificates/pending', '\\App\\Controllers\\Certificates::createPending');

    // Rotas para Marcar as aulas como completas
    $routes->group('lessons', function ($r) {
        $r->post('complete',   'LessonsController::complete');
        $r->post('uncomplete', 'LessonsController::uncomplete');
    });
});

// Rotas de Cursos
$routes->get('/courses/(:num)', 'PageController::coursePage/$1');
$routes->match(['get', 'post'], 'courses/(:num)/trial', 'PageController::startTrial/$1');
$routes->get('checkout/(:num)', 'PageController::index/$1');
$routes->post('checkout/pending/(:num)', 'Register::createPendingUser/$1');
$routes->post('student/courses/(:num)/rate', 'Student\CourseRatings::store/$1', ['filter' => 'session']);

// Pagamentos
// Pagamentos
$routes->group('mpesa', function ($routes) {
    $routes->post('send', 'MpesaController::send');
    $routes->post('status', 'MpesaController::status');
    $routes->post('callback', 'MpesaWebhookController::receive');
});

$routes->get('payment/checkout/(:num)/(:num)', 'PaymentController::createPayment/$1/$2');
$routes->post('checkout/(:num)', 'PaymentController::createPayment/$1');

// Certificados
$routes->post('certificados/emitir/(:num)', 'Certificates::emitir/$1');
// $routes->get('certificados/gerar/(:segment)', 'Certificates::gerarPdf/$1');
$routes->get('certificados/download/(:num)', 'Certificates::download/$1');
$routes->get('certificados/verificar', 'Certificates::verificar');
$routes->get('certificados/verificar/(:segment)', 'Certificates::verificar/$1');


