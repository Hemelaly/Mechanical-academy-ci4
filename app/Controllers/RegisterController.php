<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\AuthRedirect;
use App\Services\SingleDeviceSessionService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Controllers\RegisterController as ShieldRegisterController;

class RegisterController extends ShieldRegisterController
{
    /**
     * @return RedirectResponse|string
     */
    public function registerView()
    {
        AuthRedirect::fromRequest();

        if (auth()->loggedIn()) {
            return redirect()->to(config('Auth')->loginRedirect());
        }

        return parent::registerView();
    }

    public function registerAction(): RedirectResponse
    {
        AuthRedirect::fromRequest();

        if (auth()->loggedIn()) {
            return redirect()->to(config('Auth')->loginRedirect());
        }

        $cf      = new \App\Libraries\CloudflareTurnstile();
        $cfCheck = $cf->verifyRequest($this->request);
        if (! $cfCheck['ok']) {
            return redirect()->back()->withInput()->with('error', $cfCheck['message'] ?? 'Verificação Cloudflare falhou.');
        }

        $post         = $this->request->getPost();
        $post['role'] = 'student';
        $this->request->setGlobal('post', $post);

        $response = parent::registerAction();

        if (auth()->loggedIn()) {
            $user = auth()->user();
            if ($user) {
                (new SingleDeviceSessionService())->registerOnLogin((int) $user->id);
            }
        }

        return $response;
    }
}
