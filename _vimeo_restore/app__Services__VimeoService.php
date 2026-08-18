<?php

namespace App\Services;

use Config\Vimeo as VimeoConfig;
use RuntimeException;

/**
 * Cliente HTTP da Vimeo API (OAuth Bearer).
 *
 * Usado para listar pastas/vídeos e mapear para o formato
 * de módulos/aulas do criador de cursos.
 */
class VimeoService
{
    private VimeoConfig $config;

    public function __construct(?VimeoConfig $config = null)
    {
        $this->config = $config ?? config(VimeoConfig::class);
    }

    public function isReady(): bool
    {
        return $this->config->isReady();
    }

    /**
     * Valida credenciais com GET /me.
     *
     * @return array{ok:bool,user?:array,error?:string}
     */
    public function ping(): array
    {
        if (! $this->isReady()) {
            return [
                'ok'    => false,
                'error' => 'Vimeo desactivado ou access token em falta no .env.',
            ];
        }

        try {
            $me = $this->get('/me', ['fields' => 'uri,name,link,account']);

            return [
                'ok'   => true,
                'user' => [
                    'uri'  => (string) ($me['uri'] ?? ''),
                    'name' => (string) ($me['name'] ?? ''),
                    'link' => (string) ($me['link'] ?? ''),
                    'account' => (string) ($me['account'] ?? ''),
                ],
            ];
        } catch (\Throwable $e) {
            return [
                'ok'    => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * @return list<array{id:string,name:string,uri:string,link:string,privacy:string}>
     */
    public function listFolders(int $page = 1, int $perPage = 25): array
    {
        $path = $this->userPath('projects');
        $data = $this->get($path, [
            'page'     => max(1, $page),
            'per_page' => min(100, max(1, $perPage)),
            'fields'   => 'uri,name,link,privacy.name',
        ]);

        $out = [];
        foreach ($data['data'] ?? [] as $row) {
            $out[] = $this->mapFolder($row);
        }

        return $out;
    }

    /**
     * Lista vídeos de uma pasta (project/folder) Vimeo.
     *
     * @return list<array<string,mixed>>
     */
    public function listFolderVideos(string $folderId, int $page = 1, int $perPage = 100): array
    {
        $folderId = $this->extractNumericId($folderId);
        if ($folderId === '') {
            throw new RuntimeException('ID de pasta Vimeo inválido.');
        }

        $path = $this->userPath('projects/' . $folderId . '/videos');
        $data = $this->get($path, [
            'page'     => max(1, $page),
            'per_page' => min(100, max(1, $perPage)),
            'sort'     => 'alphabetical',
            'direction'=> 'asc',
            'fields'   => 'uri,name,description,duration,link,pictures.sizes,privacy.name',
        ]);

        $out = [];
        foreach ($data['data'] ?? [] as $row) {
            $out[] = $this->mapVideo($row);
        }

        return $out;
    }

    /**
     * Detalhe de um vídeo por ID ou URL Vimeo.
     *
     * @return array<string,mixed>
     */
    public function getVideo(string $videoIdOrUrl): array
    {
        $id = $this->extractVideoId($videoIdOrUrl);
        if ($id === '') {
            throw new RuntimeException('ID ou URL de vídeo Vimeo inválido.');
        }

        $row = $this->get('/videos/' . $id, [
            'fields' => 'uri,name,description,duration,link,pictures.sizes,privacy.name',
        ]);

        return $this->mapVideo($row);
    }

    /**
     * Converte vídeos de uma pasta no formato de currículo
     * usado em add/edit course (um módulo com N aulas).
     *
     * @return array{module_title:string,lessons:list<array{title:string,type:string,duration:int,video_url:string,description:string,is_preview:int}>}
     */
    public function buildCurriculumFromFolder(string $folderId, ?string $moduleTitle = null): array
    {
        $folderId = $this->extractNumericId($folderId);
        $videos = $this->listFolderVideos($folderId);

        $title = $moduleTitle;
        if ($title === null || trim($title) === '') {
            $folders = $this->listFolders(1, 100);
            foreach ($folders as $folder) {
                if ($folder['id'] === $folderId) {
                    $title = $folder['name'];
                    break;
                }
            }
            $title = $title ?: 'Módulo importado do Vimeo';
        }

        $lessons = [];
        foreach ($videos as $video) {
            $lessons[] = [
                'title'       => (string) ($video['title'] ?? 'Aula'),
                'type'        => 'video',
                'duration'    => (int) ($video['duration_minutes'] ?? 0),
                'video_url'   => (string) ($video['link'] ?? $video['player_url'] ?? ''),
                'description' => (string) ($video['description'] ?? ''),
                'is_preview'  => 0,
                'vimeo_id'    => (string) ($video['id'] ?? ''),
                'thumbnail'   => (string) ($video['thumbnail'] ?? ''),
            ];
        }

        return [
            'module_title' => $title,
            'folder_id'    => $folderId,
            'lessons'      => $lessons,
            'lesson_count' => count($lessons),
        ];
    }

    /**
     * Extrai ID numérico de pastas / vídeos a partir de URI, URL ou número.
     */
    public function extractNumericId(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }
        if (ctype_digit($value)) {
            return $value;
        }
        if (preg_match('#/(?:folders|projects|videos)/(\d+)#', $value, $m)) {
            return $m[1];
        }
        if (preg_match('/\b(\d{6,})\b/', $value, $m)) {
            return $m[1];
        }

        return '';
    }

    public function extractVideoId(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }
        if (ctype_digit($value)) {
            return $value;
        }
        if (preg_match('#vimeo\.com/(?:video/)?(\d+)#', $value, $m)) {
            return $m[1];
        }
        if (preg_match('#/videos/(\d+)#', $value, $m)) {
            return $m[1];
        }

        return $this->extractNumericId($value);
    }

    /**
     * @param array<string,mixed> $query
     * @return array<string,mixed>
     */
    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, $query);
    }

    /**
     * @param array<string,mixed> $query
     * @param array<string,mixed>|null $body
     * @return array<string,mixed>
     */
    public function request(string $method, string $path, array $query = [], ?array $body = null): array
    {
        if (! $this->isReady()) {
            throw new RuntimeException('Vimeo API não está pronta. Configure vimeo.* no .env.');
        }

        $path = '/' . ltrim($path, '/');
        $url = $this->config->apiBaseUrl . $path;
        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }

        $headers = [
            'Authorization: Bearer ' . $this->config->accessToken,
            'Accept: application/vnd.vimeo.*+json;version=' . $this->config->apiVersion,
            'Content-Type: application/json',
            'User-Agent: MechanicalAcademy/1.0',
        ];

        $ch = curl_init($url);
        if ($ch === false) {
            throw new RuntimeException('Não foi possível iniciar o pedido cURL à Vimeo.');
        }

        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => $this->config->timeoutSeconds,
            CURLOPT_CONNECTTIMEOUT => min(15, $this->config->timeoutSeconds),
        ];

        if ($body !== null) {
            $opts[CURLOPT_POSTFIELDS] = json_encode($body, JSON_UNESCAPED_UNICODE);
        }

        curl_setopt_array($ch, $opts);
        $raw = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno !== 0) {
            throw new RuntimeException('Erro de rede Vimeo: ' . $error);
        }

        $decoded = [];
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (! is_array($decoded)) {
                $decoded = ['raw' => $raw];
            }
        }

        if ($status < 200 || $status >= 300) {
            $message = (string) ($decoded['error'] ?? $decoded['developer_message'] ?? ('HTTP ' . $status));
            throw new RuntimeException('Vimeo API: ' . $message, $status);
        }

        return $decoded;
    }

    private function userPath(string $suffix): string
    {
        $suffix = ltrim($suffix, '/');
        if ($this->config->userId !== '') {
            return '/users/' . rawurlencode($this->config->userId) . '/' . $suffix;
        }

        return '/me/' . $suffix;
    }

    /**
     * @param array<string,mixed> $row
     * @return array{id:string,name:string,uri:string,link:string,privacy:string}
     */
    private function mapFolder(array $row): array
    {
        $uri = (string) ($row['uri'] ?? '');
        $id = $this->extractNumericId($uri);

        return [
            'id'      => $id,
            'name'    => (string) ($row['name'] ?? ''),
            'uri'     => $uri,
            'link'    => (string) ($row['link'] ?? ''),
            'privacy' => (string) ($row['privacy']['name'] ?? ''),
        ];
    }

    /**
     * @param array<string,mixed> $row
     * @return array<string,mixed>
     */
    private function mapVideo(array $row): array
    {
        $uri = (string) ($row['uri'] ?? '');
        $id = $this->extractNumericId($uri);
        $durationSeconds = (int) ($row['duration'] ?? 0);
        $link = (string) ($row['link'] ?? '');
        if ($link === '' && $id !== '') {
            $link = 'https://vimeo.com/' . $id;
        }

        $thumbnail = '';
        $sizes = $row['pictures']['sizes'] ?? [];
        if (is_array($sizes) && $sizes !== []) {
            $last = end($sizes);
            if (is_array($last)) {
                $thumbnail = (string) ($last['link'] ?? '');
            }
        }

        return [
            'id'               => $id,
            'title'            => (string) ($row['name'] ?? ''),
            'description'      => trim(strip_tags((string) ($row['description'] ?? ''))),
            'duration_seconds' => $durationSeconds,
            'duration_minutes' => (int) max(1, (int) ceil($durationSeconds / 60)),
            'link'             => $link,
            'player_url'       => $id !== '' ? ('https://player.vimeo.com/video/' . $id) : '',
            'thumbnail'        => $thumbnail,
            'privacy'          => (string) ($row['privacy']['name'] ?? ''),
            'uri'              => $uri,
        ];
    }
}
