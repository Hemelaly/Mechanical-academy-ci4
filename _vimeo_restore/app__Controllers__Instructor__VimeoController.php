<?php

namespace App\Controllers\Instructor;

use App\Controllers\BaseController;
use App\Services\VimeoService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Endpoints JSON para puxar pastas/vídeos do Vimeo
 * e pré-preencher o currículo do curso.
 */
class VimeoController extends BaseController
{
    public function status(): ResponseInterface
    {
        $vimeo = service('vimeo');
        $cfg = config(\Config\Vimeo::class);

        $ping = $vimeo->ping();

        return $this->response->setJSON([
            'ok'            => (bool) ($ping['ok'] ?? false),
            'enabled'       => $cfg->enabled,
            'ready'         => $vimeo->isReady(),
            'has_oauth_app' => $cfg->hasOAuthApp(),
            'user'          => $ping['user'] ?? null,
            'error'         => $ping['error'] ?? null,
        ]);
    }

    public function folders(): ResponseInterface
    {
        try {
            $page = max(1, (int) $this->request->getGet('page'));
            $perPage = (int) ($this->request->getGet('per_page') ?? 25);
            $folders = service('vimeo')->listFolders($page, $perPage);

            return $this->response->setJSON([
                'ok'      => true,
                'folders' => $folders,
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(502)->setJSON([
                'ok'    => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function folderCurriculum(string $folderId): ResponseInterface
    {
        try {
            $moduleTitle = trim((string) ($this->request->getGet('title') ?? ''));
            $curriculum = service('vimeo')->buildCurriculumFromFolder(
                $folderId,
                $moduleTitle !== '' ? $moduleTitle : null
            );

            return $this->response->setJSON([
                'ok'         => true,
                'curriculum' => $curriculum,
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(502)->setJSON([
                'ok'    => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function video(): ResponseInterface
    {
        $idOrUrl = trim((string) ($this->request->getGet('id') ?? $this->request->getGet('url') ?? ''));
        if ($idOrUrl === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'    => false,
                'error' => 'Informe id= ou url= do vídeo Vimeo.',
            ]);
        }

        try {
            $video = service('vimeo')->getVideo($idOrUrl);

            return $this->response->setJSON([
                'ok'    => true,
                'video' => $video,
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(502)->setJSON([
                'ok'    => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
