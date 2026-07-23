<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Meta (Facebook) Pixel — tracking no checkout.
 */
class MetaPixel extends BaseConfig
{
    public bool $enabled = false;

    /** Pixel ID (ex.: 2149504535607448) */
    public string $pixelId = '';

    public function __construct()
    {
        parent::__construct();

        $raw = env('meta.pixel.enabled', $this->enabled);
        $this->enabled = is_bool($raw)
            ? $raw
            : in_array(strtolower(trim((string) $raw)), ['1', 'true', 'yes', 'on'], true);

        $this->pixelId = trim((string) env('meta.pixel.id', $this->pixelId));

        if ($this->pixelId === '') {
            $this->enabled = false;
        }
    }

    public function isReady(): bool
    {
        return $this->enabled && $this->pixelId !== '';
    }
}
