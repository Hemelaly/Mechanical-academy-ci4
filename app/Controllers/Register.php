<?php

namespace App\Controllers;

use App\Models\StudentModel;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\IncomingRequest;

class Register extends Controller
{
    public function register()
    {
        $request = service('request');
        $post = $request->getPost();

        $userModel = new UserModel();

        // Cria usuário no Shield
        $userId = $userModel->insert([
            'username' => $post['username'],
            'email'    => $post['email'],
            'password' => $post['password'],
        ], true); // true para retornar o ID

        // Adiciona na tabela students
        $studentModel = new StudentModel();
        $studentModel->insert([
            'id_user_student' => $userId,
            'name_student'    => $post['username'],
            'email_student'   => $post['email'],
        ]);

        return redirect()->to('/login')->with('success', 'Cadastro concluído!');
    }
}
