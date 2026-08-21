<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\AuthRedirect;
use App\Services\SingleDeviceSessionService;
use App\Services\StudentAccountService;
use Config\Google;
use GuzzleHttp\Client;

class GoogleAuthController extends BaseController
{
    public function redirect()
    {
        AuthRedirect::fromRequest();

        if (auth()->loggedIn()) {
            return redirect()->to(config('Auth')->loginRedirect());
        }

        $google = config(Google::class);
        if (! $google->isConfigured()) {
            return redirect()->to(site_url('login'))->with('error', 'Login com Google ainda não está configurado.');
        }

        $state = bin2hex(random_bytes(16));
        session()->set('google_oauth_state', $state);

        $query = http_build_query([
            'client_id'     => $google->clientId,
            'redirect_uri'  => $google->callbackUrl(),
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $state,
            'access_type'   => 'online',
            'prompt'        => 'select_account',
        ]);

        return redirect()->to($google->authUrl . '?' . $query);
    }

    public function callback()
    {
        if (auth()->loggedIn()) {
            return redirect()->to(config('Auth')->loginRedirect());
        }

        $google = config(Google::class);
        if (! $google->isConfigured()) {
            return redirect()->to(site_url('login'))->with('error', 'Login com Google ainda não está configurado.');
        }

        $error = trim((string) $this->request->getGet('error'));
        if ($error !== '') {
            return redirect()->to(site_url('login'))->with('error', 'Login com Google cancelado.');
        }

        $state         = (string) $this->request->getGet('state');
        $expectedState = (string) session()->get('google_oauth_state');
        session()->remove('google_oauth_state');

        if ($state === '' || $expectedState === '' || ! hash_equals($expectedState, $state)) {
            return redirect()->to(site_url('login'))->with('error', 'Sessão Google inválida. Tente novamente.');
        }

        $code = trim((string) $this->request->getGet('code'));
        if ($code === '') {
            return redirect()->to(site_url('login'))->with('error', 'Não foi possível autenticar com o Google.');
        }

        try {
            $profile = $this->fetchGoogleProfile($google, $code);
        } catch (\Throwable $e) {
            log_message('error', 'Google OAuth falhou: {error}', ['error' => $e->getMessage()]);

            return redirect()->to(site_url('login'))->with('error', 'Não foi possível concluir o login com Google.');
        }

        $googleId = trim((string) ($profile['sub'] ?? ''));
        $email    = trim(strtolower((string) ($profile['email'] ?? '')));
        $name     = trim((string) ($profile['name'] ?? ''));

        if ($googleId === '' || $email === '') {
            return redirect()->to(site_url('login'))->with('error', 'A conta Google não devolveu um e-mail válido.');
        }

        $accounts = new StudentAccountService();
        $user     = $accounts->findByGoogleId($googleId) ?: $accounts->findByEmail($email);

        try {
            if (! $user) {
                $user = $accounts->createStudent($email, $name !== '' ? $name : $email);
            } else {
                $accounts->ensureStudentProfile($user, $name !== '' ? $name : (string) ($user->username ?? $email), $email);
            }

            $accounts->linkGoogle($user, $googleId, $email);
            $accounts->completeLogin($user);

            $devices = new SingleDeviceSessionService();
            $claim   = $devices->claimOrReject((int) $user->id);
            if (! ($claim['ok'] ?? false)) {
                try {
                    auth()->logout();
                } catch (\Throwable $e) {
                    // ignore
                }
                session()->remove(SingleDeviceSessionService::SESSION_KEY);

                return redirect()->to(site_url('login'))->with(
                    'error',
                    $claim['message'] ?? 'Esta conta já está ligada noutro dispositivo.'
                );
            }
        } catch (\Throwable $e) {
            log_message('error', 'Google login/cadastro falhou: {error}', ['error' => $e->getMessage()]);

            return redirect()->to(site_url('login'))->with('error', 'Não foi possível criar ou entrar na conta Google.');
        }

        return redirect()->to(config('Auth')->loginRedirect())
            ->with('message', 'Sessão iniciada com Google.');
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchGoogleProfile(Google $google, string $code): array
    {
        $http = new Client([
            'timeout'         => 20,
            'connect_timeout' => 10,
        ]);

        $tokenResponse = $http->post($google->tokenUrl, [
            'form_params' => [
                'code'          => $code,
                'client_id'     => $google->clientId,
                'client_secret' => $google->clientSecret,
                'redirect_uri'  => $google->callbackUrl(),
                'grant_type'    => 'authorization_code',
            ],
        ]);

        $token = json_decode((string) $tokenResponse->getBody(), true);
        $accessToken = is_array($token) ? trim((string) ($token['access_token'] ?? '')) : '';

        if ($accessToken === '') {
            throw new \RuntimeException('Token Google em falta.');
        }

        $userResponse = $http->get($google->userInfoUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        $profile = json_decode((string) $userResponse->getBody(), true);
        if (! is_array($profile)) {
            throw new \RuntimeException('Perfil Google inválido.');
        }

        return $profile;
    }
}
