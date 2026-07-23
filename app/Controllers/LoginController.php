<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Controllers\LoginController as ShieldLoginController;

/**
 * Evita LogicException do Shield quando a sessão ainda tem user info
 * (login pendente / sessão órfã / reenvio do formulário já autenticado).
 */
class LoginController extends ShieldLoginController
{
    /**
     * @return RedirectResponse|string
     */
    public function loginView()
    {
        $this->prepareLoginSession();

        if (auth()->loggedIn()) {
            return redirect()->to(config('Auth')->loginRedirect());
        }

        /** @var Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();

        if ($authenticator->hasAction()) {
            return redirect()->route('auth-action-show');
        }

        return $this->view(setting('Auth.views')['login']);
    }

    public function loginAction(): RedirectResponse
    {
        $this->prepareLoginSession();

        if (auth()->loggedIn()) {
            return redirect()->to(config('Auth')->loginRedirect());
        }

        $cf = new \App\Libraries\CloudflareTurnstile();
        $cfCheck = $cf->verifyRequest($this->request);
        if (! $cfCheck['ok']) {
            return redirect()->back()->withInput()->with('error', $cfCheck['message'] ?? 'Verificação Cloudflare falhou.');
        }

        /** @var Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();

        if ($authenticator->isPending() || $authenticator->hasAction()) {
            return redirect()->route('auth-action-show');
        }

        return parent::loginAction();
    }

    public function logoutAction(): RedirectResponse
    {
        $url = config('Auth')->logoutRedirect();

        try {
            auth()->logout();
        } catch (\Throwable $e) {
            log_message('error', 'Logout falhou: {error}', ['error' => $e->getMessage()]);
        }

        $this->purgeAuthSession();

        return redirect()->to($url)->with('message', lang('Auth.successLogout'));
    }

    /**
     * Garante que não fica user_id na sessão antes de um novo attempt().
     */
    private function prepareLoginSession(): void
    {
        /** @var Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();

        // Já autenticado ou a meio de 2FA/activação — não limpar.
        if (auth()->loggedIn() || $authenticator->isPending() || $authenticator->hasAction()) {
            return;
        }

        $this->purgeAuthSession();
    }

    private function purgeAuthSession(): void
    {
        $field = setting('Auth.sessionConfig')['field'] ?? 'user';

        if (session()->has($field)) {
            session()->remove($field);
        }
    }
}
