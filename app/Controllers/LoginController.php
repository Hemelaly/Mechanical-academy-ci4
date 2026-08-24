<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\AuthRedirect;
use App\Services\SingleDeviceSessionService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Controllers\LoginController as ShieldLoginController;

/**
 * Login livre em qualquer dispositivo.
 * O limite de 1 dispositivo aplica-se só à visualização de vídeos.
 */
class LoginController extends ShieldLoginController
{
    /**
     * @return RedirectResponse|string
     */
    public function loginView()
    {
        $this->prepareLoginSession();
        AuthRedirect::fromRequest();

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

        $rules = $this->getValidationRules();
        if (! $this->validateData($this->request->getPost(), $rules, [], config('Auth')->DBGroup)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        /** @var array $credentials */
        $credentials             = $this->request->getPost(setting('Auth.validFields')) ?? [];
        $credentials             = array_filter($credentials);
        $credentials['password'] = $this->request->getPost('password');
        $remember                = (bool) $this->request->getPost('remember');

        $result = $authenticator->remember($remember)->attempt($credentials);
        if (! $result->isOK()) {
            return redirect()->route('login')->withInput()->with('error', $result->reason());
        }

        if ($authenticator->hasAction()) {
            return redirect()->route('auth-action-show')->withCookies();
        }

        $user = auth()->user();
        if ($user) {
            (new SingleDeviceSessionService())->registerOnLogin((int) $user->id);
        }

        return redirect()->to(config('Auth')->loginRedirect())->withCookies();
    }

    public function logoutAction(): RedirectResponse
    {
        $url = config('Auth')->logoutRedirect();
        $userId = auth()->loggedIn() ? (int) (auth()->user()->id ?? 0) : 0;

        // Liberta o lock de vídeos só se este dispositivo for o primário
        // (2.º dispositivo a fazer logout não deve desbloquear o 1.º).
        (new SingleDeviceSessionService())->release($userId);

        try {
            auth()->logout();
        } catch (\Throwable $e) {
            log_message('error', 'Logout falhou: {error}', ['error' => $e->getMessage()]);
        }

        $this->purgeAuthSession();
        session()->remove(SingleDeviceSessionService::SESSION_KEY);

        return redirect()->to($url)->with('message', lang('Auth.successLogout'));
    }

    private function prepareLoginSession(): void
    {
        /** @var Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();

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
