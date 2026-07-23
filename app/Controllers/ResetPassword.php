<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PasswordResetModel;
use App\Services\CheckoutEnrollmentService;
use CodeIgniter\Shield\Models\UserModel;

class ResetPassword extends BaseController
{
    public function showResetForm()
    {
        $token = trim((string) $this->request->getGet('token'));
        if ($token === '') {
            return view('auth/forgot_password');
        }

        return view('reset_password', [
            'token'  => $token,
            'next'   => trim((string) $this->request->getGet('next')),
            'course' => trim((string) $this->request->getGet('course')),
        ]);
    }

    public function requestReset()
    {
        $email = trim((string) $this->request->getPost('email'));

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->to(site_url('reset-password'))->withInput()->with('error', 'Informe um email valido.');
        }

        $users = auth()->getProvider();
        if ($users === null) {
            $users = new UserModel();
        }

        $user = $users->findByCredentials(['email' => $email]);

        if ($user) {
            $passwordResetModel = new PasswordResetModel();
            $passwordResetModel->where('user_id', $user->id)->delete();

            $token = bin2hex(random_bytes(16));
            $expires = date('Y-m-d H:i:s', strtotime('+1 day'));

            $passwordResetModel->insert([
                'user_id'    => $user->id,
                'token'      => $token,
                'expires_at' => $expires,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $link = site_url('reset-password') . '?' . http_build_query(['token' => $token]);

            $mail = \Config\Services::email();
            $mail->setTo($user->email);
            $mail->setSubject('Redefinição de senha');
            $mail->setMailType('html');
            $mail->setMessage(\App\Libraries\BrandEmail::render([
                'preheader' => 'Use o link para redefinir a sua palavra-passe.',
                'eyebrow'   => 'Segurança da conta',
                'greeting'  => 'Olá ' . \App\Libraries\BrandEmail::strong((string) $user->username) . ',',
                'title'     => 'Redefinir a sua palavra-passe',
                'body'      => \App\Libraries\BrandEmail::p(
                    'Recebemos um pedido para redefinir a palavra-passe da sua conta na Mechanical Academy.'
                ) . \App\Libraries\BrandEmail::p(
                    'O link é válido por tempo limitado. Se não solicitou esta alteração, ignore este email.'
                ),
                'cta' => [
                    'url'   => $link,
                    'label' => 'Redefinir senha',
                ],
            ]));
            $mail->send();
        }

        return redirect()->to(site_url('reset-password'))->with('message', 'Se o email existir, enviaremos um link de redefinicao.');
    }

    public function submitReset()
    {
        $post = $this->request->getPost();
        $token = trim((string) ($post['token'] ?? ''));
        $password = (string) ($post['password'] ?? '');
        $confirm = (string) ($post['password_confirm'] ?? '');
        $next = trim((string) ($post['next'] ?? ''));
        $course = trim((string) ($post['course'] ?? ''));

        if ($token === '' || $password === '' || $confirm === '') {
            return redirect()->back()->withInput()->with('error', 'Preencha todos os campos.');
        }

        if (strlen($password) < 8) {
            return redirect()->back()->withInput()->with('error', 'A senha deve ter pelo menos 8 caracteres.');
        }

        if ($confirm !== $password) {
            return redirect()->back()->withInput()->with('error', 'A confirmacao da senha nao confere.');
        }

        $passwordResetModel = new PasswordResetModel();
        $reset = $passwordResetModel->where('token', $token)->first();

        if ($reset) {
            if (! empty($reset['expires_at']) && strtotime((string) $reset['expires_at']) < time()) {
                return redirect()->back()->withInput()->with('error', 'Token expirado.');
            }

            $users = auth()->getProvider();
            if ($users === null) {
                $users = new UserModel();
            }

            $user = $users->find((int) $reset['user_id']);
            if (! $user) {
                return redirect()->back()->withInput()->with('error', 'Usuario nao encontrado.');
            }

            $user->password = $password;
            if (! $users->save($user)) {
                $errors = $users->errors();

                return redirect()->back()->withInput()->with(
                    'error',
                    $errors ? implode(', ', $errors) : 'Nao foi possivel atualizar a senha.'
                );
            }

            $passwordResetModel->where('user_id', $user->id)->delete();

            session()->regenerate(true);
            auth()->login($user);

            $target = $this->isSafeLocalPath($next) ? $next : '/student/dashboard';
            $swal = [
                'icon'  => 'success',
                'title' => 'Senha criada',
                'text'  => $course !== ''
                    ? 'Senha criada com sucesso. Voce foi inscrito no curso ' . $course . '.'
                    : 'Sua senha foi redefinida com sucesso.',
            ];

            return redirect()->to($target)->with('swal', $swal);
        }

        try {
            $result = (new CheckoutEnrollmentService())->completePendingUserCheckout($token, $password);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage() ?: 'Token invalido.');
        }

        session()->regenerate(true);
        auth()->login($result['user']);

        $target = $this->isSafeLocalPath($next)
            ? $next
            : ($result['course_path'] ?? '/student/dashboard');

        $swal = [
            'icon'  => 'success',
            'title' => 'Conta ativada',
            'text'  => $course !== ''
                ? 'Senha criada com sucesso. O seu acesso ao curso ' . $course . ' foi ativado.'
                : 'Senha criada com sucesso. A sua conta foi ativada.',
        ];

        return redirect()->to($target)->with('swal', $swal);
    }

    private function isSafeLocalPath(string $path): bool
    {
        return $path !== '' && str_starts_with($path, '/') && ! str_starts_with($path, '//');
    }
}
