<?php

// app/Filters/MagicLoginRedirect.php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class MagicLoginRedirect implements FilterInterface
{
    
    private array $whitelist = [
        'reset-password', 'reset-password/*',
        'login', 'login/*', 'logout',
        'auth/a/*', // endpoints do Shield
        'assets/*','css/*','js/*','images/*','img/*','media/*',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $path = trim($request->getUri()->getPath(), '/');

        // Se já está em rota liberada, não faz nada
        if ($this->isWhitelisted($path)) {
            return;
        }

        // Só atua se logado
        if (! auth()->loggedIn()) {
            return;
        }

        $user    = auth()->user();
        $magic   = (bool) session('magicLogin');
        $force   = method_exists($user, 'requiresPasswordReset') && $user->requiresPasswordReset();

        if ($magic || $force) {
            // manda para reset apenas se não estiver em rota liberada
            return redirect()->to('/reset-password');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nada aqui
    }

    private function isWhitelisted(string $path): bool
    {
        foreach ($this->whitelist as $rule) {
            $rule = rtrim($rule, '/');
            // coringa simples
            if (str_ends_with($rule, '*')) {
                $base = rtrim(substr($rule, 0, -1), '/');
                if ($base === '' || str_starts_with($path, $base)) return true;
            } elseif ($path === trim($rule, '/')) {
                return true;
            }
        }
        return false;
    }
}
