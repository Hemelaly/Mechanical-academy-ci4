<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * PostHog product analytics (frontend).
 *
 * Project settings: https://app.posthog.com → Project → Project API Key
 */
class Posthog extends BaseConfig
{
    public bool $enabled = false;

    /** Project API Key (phc_...) — safe to expose in frontend */
    public string $apiKey = '';

    /** Ex.: https://us.i.posthog.com ou https://eu.i.posthog.com */
    public string $apiHost = 'https://us.i.posthog.com';

    public bool $capturePageview = true;

    public bool $capturePageleave = true;

    public function __construct()
    {
        parent::__construct();

        $enabledRaw = env('posthog.enabled', $this->enabled);
        if (is_bool($enabledRaw)) {
            $this->enabled = $enabledRaw;
        } else {
            $this->enabled = in_array(strtolower(trim((string) $enabledRaw)), ['1', 'true', 'yes', 'on'], true);
        }

        $this->apiKey = trim((string) env('posthog.apiKey', $this->apiKey));
        $this->apiHost = rtrim(trim((string) env('posthog.apiHost', $this->apiHost)), '/');

        $pv = env('posthog.capturePageview', $this->capturePageview);
        $this->capturePageview = is_bool($pv)
            ? $pv
            : in_array(strtolower(trim((string) $pv)), ['1', 'true', 'yes', 'on'], true);

        $pl = env('posthog.capturePageleave', $this->capturePageleave);
        $this->capturePageleave = is_bool($pl)
            ? $pl
            : in_array(strtolower(trim((string) $pl)), ['1', 'true', 'yes', 'on'], true);

        if ($this->apiKey === '') {
            $this->enabled = false;
        }
    }

    public function isReady(): bool
    {
        return $this->enabled && $this->apiKey !== '';
    }
}
