<?php

namespace Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\FrameworkException;
use CodeIgniter\Shield\Authentication\Events\AuthEvents;
use CodeIgniter\HotReloader\HotReloader;
use App\Models\StudentModel;
use App\Models\InstructorModel;
use App\Models\ExtendedUserModel;

/*
 * --------------------------------------------------------------------
 * Application Events
 * --------------------------------------------------------------------
 * Events allow you to tap into the execution of the program without
 * modifying or extending core files. This file provides a central
 * location to define your events, though they can always be added
 * at run-time, also, if needed.
 *
 * You create code that can execute by subscribing to events with
 * the 'on()' method. This accepts any form of callable, including
 * Closures, that will be executed when the event is triggered.
 *
 * Example:
 *      Events::on('create', [$myInstance, 'myMethod']);
 */
Events::on('register', function ($user) {
    $request = service('request');
    $post = $request->getPost();

    // Pega os dados personalizados do formulario
    $email    = $post['email'] ?? null;
    $username = $post['username'] ?? null;
    $role     = $post['role'] ?? 'student';

    $allowedRoles = ['student', 'instructor', 'admin'];
    if (! in_array($role, $allowedRoles, true)) {
        $role = 'student';
    }

    // Atualiza o role do usuario
    $userModel = new ExtendedUserModel();
    $userModel->update($user->id, ['role' => $role]);

    if ($role === 'student') {
        // Inserir na tabela students
        $studentModel = new StudentModel();
        $studentModel->insert([
            'id_user_student' => $user->id,  // chave estrangeira para tabela users
            'email_student'   => $email,
            'name_student'    => $username
        ]);
    } elseif ($role === 'instructor') {
        // Inserir na tabela instructors
        $instructorModel = new InstructorModel();
        $instructorModel->insert([
            'id_user_instructor' => $user->id,
            'email_instructor'   => $email,
            'name_instructor'    => $username
        ]);
    }
});

Events::on('login', function ($user) {
    // Verifica role
    if ($user->role === 'admin') {
        return redirect()->to('/admin/dashboard/');
    } elseif ($user->role === 'instructor') {
        return redirect()->to('/instructor/dashboard/');
    } elseif ($user->role === 'student') {
        return redirect()->to('/student/dashboard/');
    }
    
    // fallback
    return redirect()->to('/');
});

Events::on('pre_system', static function (): void {
    if (ENVIRONMENT !== 'testing') {
        if (ini_get('zlib.output_compression')) {
            throw FrameworkException::forEnabledZlibOutputCompression();
        }

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        ob_start(static fn($buffer) => $buffer);
    }

    // Alinha o fuso da sessão MySQL com Africa/Maputo (UTC+2, sem DST).
    try {
        $tz = new \DateTimeZone(app_timezone());
        $offset = $tz->getOffset(new \DateTime('now', $tz));
        $sign = $offset < 0 ? '-' : '+';
        $offset = abs($offset);
        $hours = intdiv($offset, 3600);
        $mins = intdiv($offset % 3600, 60);
        $mysqlTz = sprintf('%s%02d:%02d', $sign, $hours, $mins);
        $db = db_connect();
        $db->query('SET time_zone = ' . $db->escape($mysqlTz));
    } catch (\Throwable $e) {
        // Não bloquear o boot se a BD ainda não estiver disponível.
        log_message('debug', 'Falha ao definir time_zone MySQL: ' . $e->getMessage());
    }

    /*
     * --------------------------------------------------------------------
     * Debug Toolbar Listeners.
     * --------------------------------------------------------------------
     * If you delete, they will no longer be collected.
     */
    if (CI_DEBUG && ! is_cli()) {
        Events::on('DBQuery', 'CodeIgniter\Debug\Toolbar\Collectors\Database::collect');
        service('toolbar')->respond();
        // Hot Reload route - for framework use on the hot reloader.
        if (ENVIRONMENT === 'development') {
            service('routes')->get('__hot-reload', static function (): void {
                (new HotReloader())->run();
            });
        }
    }
});
