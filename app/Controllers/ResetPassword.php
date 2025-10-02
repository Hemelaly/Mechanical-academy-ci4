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
            return redirect()->to('/')->with('error', 'Token inválido.');
        }

        return view('reset_password', ['token' => $token]);
    }

    public function submitReset()
    {
        $token    = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        if (!$token || !$password) {
            return redirect()->back()->with('error', 'Preencha todos os campos.');
        }

        $passwordResetModel = new PasswordResetModel();
        $reset = $passwordResetModel->where('token', $token)->first();

        if (!$reset || strtotime($reset['expires_at']) < time()) {
            return redirect()->back()->with('error', 'Token expirado ou inválido.');
        }

        // Recupera o usuário
        $userModel = new UserModel();
        $user = $userModel->find($reset['user_id']);

        if (!$user) {
            return redirect()->back()->with('error', 'Usuário não encontrado.');
        }

        // Atualiza a senha
        $user->fill([
            'password' => $password, // Shield faz o hash automaticamente
        ]);

        $userModel->save($user);

        // Remove o token
        $passwordResetModel->delete($reset['id']);

        // Garante que não exista sessão ativa
        service('auth')->logout();

        // Redireciona pro login
        return redirect()->to('/login')
            ->with('success', 'Senha criada com sucesso! Faça login para começar a estudar.');
    }
}
