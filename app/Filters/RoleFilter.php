<?php

namespace App\Filters;

use App\Services\SingleDeviceSessionService;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = service('auth');
        $session = session();

        $user = $auth->user();

        if (! $user) {
            $session->setFlashdata('error', 'Você precisa estar logado para acessar esta página.');
            return redirect()->to(site_url('login'));
        }

        // Não desloga o 2.º dispositivo — só mantém heartbeat se for o primário dos vídeos.
        (new SingleDeviceSessionService())->touchIfPrimary((int) $user->id);

        if (! in_array($user->role, $arguments ?? [], true)) {
            $session->setFlashdata('error', 'Acesso negado. Você não tem permissão para acessar esta página.');
            return redirect()->to(site_url('/'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // não precisa fazer nada depois
    }
}
