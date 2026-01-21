<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PasswordResetModel as ModelsPasswordResetModel;
use App\Models\PasswordResetModel;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Entities\User;

class ResetPassword extends BaseController
{
    public function showResetForm()
    {
        $token = $this->request->getGet('token');
        if (!$token) {
            return view('auth/forgot_password');
        }

        return view('reset_password', ['token' => $token]);
    }

    public function requestReset()
    {
        $email = trim((string) $this->request->getPost('email'));

        if ($email == '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Informe um email valido.');
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

            $link = site_url("reset-password?token={$token}");

            $mail = \Config\Services::email();
            $mail->setTo($user->email);
            $mail->setSubject('Redefinicao de senha');
            $mail->setMessage(
                "Ola {$user->username},<br><br>" .
                "Clique no link abaixo para redefinir sua senha:<br><br>" .
                "<a href=\"{$link}\">Redefinir senha</a>"
            );
            $mail->send();
        }

        return redirect()->back()->with('message', 'Se o email existir, enviaremos um link de redefinicao.');
    }

    public function submitReset()
    {
        $post     = $this->request->getPost();
        $token    = trim((string)($post['token'] ?? ''));
        $password = (string)($post['password'] ?? '');
        $confirm  = array_key_exists('password_confirm', $post) ? (string)$post['password_confirm'] : null;

        if ($token === '' || $password === '') {
            return redirect()->back()->with('error', 'Preencha todos os campos.');
        }
        if (strlen($password) < 8) {
            return redirect()->back()->with('error', 'A senha deve ter pelo menos 8 caracteres.');
        }
        if ($confirm !== null && $confirm !== $password) {
            return redirect()->back()->with('error', 'A confirmação da senha não confere.');
        }

        $passwordResetModel = new PasswordResetModel();
        $reset = $passwordResetModel->where('token', $token)->first();
        if (!$reset) {
            return redirect()->back()->with('error', 'Token inválido.');
        }
        if (isset($reset['expires_at']) && strtotime($reset['expires_at']) < time()) {
            return redirect()->back()->with('error', 'Token expirado.');
        }

        // ---------- AQUI ESTÁ A CORREÇÃO ----------
        // Tente obter o provider do Shield (melhor caminho):
        $users = auth()->getProvider();
        // Se por algum motivo vier null, instancia direto o model do Shield:
        if ($users === null) {
            $users = new ShieldUserModel(); // \CodeIgniter\Shield\Models\UserModel
        }
        // -----------------------------------------

        $user = $users->find((int) $reset['user_id']);
        if (!$user) {
            return redirect()->back()->with('error', 'Usuário não encontrado.');
        }

        // Atualiza a senha (Shield faz hash em password_hash via mutator)
        $user->password = $password;
        if (!$users->save($user)) {
            $errs = $users->errors();
            return redirect()->back()->with('error', $errs ? implode(', ', $errs) : 'Não foi possível atualizar a senha.');
        }

        // Apaga tokens desse usuário
        $passwordResetModel->where('user_id', $user->id)->delete();

        // Regenera sessão e faz login automático
        session()->regenerate(true);
        auth()->login($user);

        return redirect()->to('/login')->with('swal', [
            'icon'  => 'success',
            'title' => 'Senha atualizada',
            'text'  => 'Sua senha foi redefinida com sucesso. Faça login novamente.',
        ]);
    }
}
