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

        $devices = new SingleDeviceSessionService();
        if (! $devices->assertCurrentDevice((int) $user->id)) {
            try {
                $auth->logout();
            } catch (\Throwable $e) {
                // ignore
            }
            $session->remove(SingleDeviceSessionService::SESSION_KEY);
            $session->setFlashdata(
                'error',
                'Esta conta está activa noutro dispositivo. Faça logout lá para entrar aqui.'
            );

            return redirect()->to(site_url('login'));
        }

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
