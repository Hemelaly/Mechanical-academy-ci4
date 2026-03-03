<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Jitsi extends BaseConfig
{
    public bool $enabled = true;

    /**
     * Ex.: meet.jit.si, 8x8.vc, meet.seudominio.com
     */
    public string $domain = 'meet.jit.si';

    /**
     * Para JaaS, normalmente o roomName usa "<appId>/<room>".
     */
    public string $appId = '';

    /**
     * Prefixo local para separar salas da aplicação.
     */
    public string $roomPrefix = 'academy';

    /**
     * JWT
     */
    public string $jwtKeyId = '';
    public string $jwtApiKey = '';
    public string $jwtAudience = 'jitsi';
    public string $jwtIssuer = 'chat';
    public string $jwtSubject = 'meet.jit.si';
    public string $jwtRoom = '';
    public int $jwtTtlSeconds = 7200;
    public string $jwtPrivateKey = '';
    public string $jwtPrivateKeyPath = '';

    /**
     * Gravação default para comandos startRecording.
     * Valores comuns: file | stream | local
     */
    public string $defaultRecordingMode = 'file';

    public function __construct()
    {
        parent::__construct();

        $this->enabled = filter_var(env('jitsi.enabled', $this->enabled), FILTER_VALIDATE_BOOL);
        $this->domain = (string) env('jitsi.domain', $this->domain);
        $this->appId = trim((string) env('jitsi.appId', $this->appId));
        $this->roomPrefix = trim((string) env('jitsi.roomPrefix', $this->roomPrefix));

        $this->jwtApiKey = trim((string) env('jitsi.jwtApiKey', $this->jwtApiKey));
        $this->jwtKeyId = trim((string) env('jitsi.jwtKeyId', $this->jwtKeyId));
        if ($this->jwtKeyId === '' && $this->jwtApiKey !== '') {
            $this->jwtKeyId = $this->jwtApiKey;
        }
        $this->jwtAudience = trim((string) env('jitsi.jwtAudience', $this->jwtAudience));
        $this->jwtIssuer = trim((string) env('jitsi.jwtIssuer', $this->jwtIssuer));
        $this->jwtSubject = trim((string) env('jitsi.jwtSubject', $this->jwtSubject));
        $this->jwtRoom = trim((string) env('jitsi.jwtRoom', $this->jwtRoom));
        $this->jwtTtlSeconds = (int) env('jitsi.jwtTtlSeconds', (string) $this->jwtTtlSeconds);
        $this->jwtPrivateKey = (string) env('jitsi.jwtPrivateKey', $this->jwtPrivateKey);
        $this->jwtPrivateKeyPath = (string) env('jitsi.jwtPrivateKeyPath', $this->jwtPrivateKeyPath);
        $this->defaultRecordingMode = (string) env('jitsi.defaultRecordingMode', $this->defaultRecordingMode);
    }
}
