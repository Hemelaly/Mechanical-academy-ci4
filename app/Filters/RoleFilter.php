<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = service('auth');
        $session = session();

        $user = $auth->user(); // retorna null se não estiver logado

        if (!$user) {
            $session->setFlashdata('error', 'Você precisa estar logado para acessar esta página.');
            return redirect()->to(site_url('login'));
        }

        // Verifica se a role do usuário está nos argumentos do filtro
        if (!in_array($user->role, $arguments)) {
            $session->setFlashdata('error', 'Acesso negado. Você não tem permissão para acessar esta página.');
            return redirect()->to(site_url('/'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // não precisa fazer nada depois
    }
}
