<?php

namespace Config;

use CodeIgniter\Shield\Config\Auth as ShieldAuth;
use CodeIgniter\Shield\Models\UserModel;

class Auth extends ShieldAuth
{
    /**
     * --------------------------------------------------------------------
     * Redirect URLs
     * --------------------------------------------------------------------
     */
    public array $redirects = [
        'register'          => '/',
        'login'             => '/',   // será sobrescrito pelo método loginRedirect()
        'logout'            => '/auth/login',
        'force_reset'       => '/',
        'permission_denied' => '/',
        'group_denied'      => '/',
    ];

    /**
     * --------------------------------------------------------------------
     * User Provider
     * --------------------------------------------------------------------
     */
    public string $userProvider = UserModel::class;

    /**
     * --------------------------------------------------------------------
     * Autenticators
     * --------------------------------------------------------------------
     */
    public array $authenticators = [
        'tokens'  => \CodeIgniter\Shield\Authentication\Authenticators\AccessTokens::class,
        'session' => \CodeIgniter\Shield\Authentication\Authenticators\Session::class,
        'hmac'    => \CodeIgniter\Shield\Authentication\Authenticators\HmacSha256::class,
    ];

    public string $defaultAuthenticator = 'session';

    /**
     * --------------------------------------------------------------------
     * Login Redirect por role
     * --------------------------------------------------------------------
     */
    public function loginRedirect(): string
    {
        $user = service('auth')->user();

        if (!$user) {
            // fallback padrão
            return $this->getUrl(setting('Auth.redirects')['login']);
        }

        return match ($user->role) {
            'admin'      => $this->getUrl('/admin/dashboard'),
            'instructor' => $this->getUrl('/instructor/dashboard'),
            'student'    => $this->getUrl('/student/dashboard'),
            default      => $this->getUrl('/'),
        };
    }

    /**
     * --------------------------------------------------------------------
     * Logout redirect
     * --------------------------------------------------------------------
     */
    public function logoutRedirect(): string
    {
        return $this->getUrl('/login');
    }

    /**
     * --------------------------------------------------------------------
     * Método helper para converter rota/URI em URL completa
     * --------------------------------------------------------------------
     */
    protected function getUrl(string $url): string
    {
        return match (true) {
            str_starts_with($url, 'http://') || str_starts_with($url, 'https://') => $url,
            route_to($url) !== false                                              => rtrim(url_to($url), '/ '),
            default                                                               => rtrim(site_url($url), '/ '),
        };
    }
}
