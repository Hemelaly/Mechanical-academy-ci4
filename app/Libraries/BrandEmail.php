<?php

namespace App\Libraries;

/**
 * Template HTML branded para emails da Mechanical Academy.
 * Cores: accent #0d6efd · fundo #050505 · tipografia alinhada à home.
 */
class BrandEmail
{
    public const ACCENT = '#0d6efd';
    public const ACCENT_SOFT = '#6ea8fe';
    public const BG_DARK = '#050505';
    public const SURFACE = '#141414';
    public const CARD = '#ffffff';
    public const INK = '#1a1d23';
    public const MUTED = '#5c6570';

    /**
     * @param array{
     *   preheader?: string,
     *   eyebrow?: string,
     *   title?: string,
     *   greeting?: string,
     *   body?: string,
     *   info?: list<array{label: string, value: string}>,
     *   cta?: array{url: string, label: string}|null,
     *   secondary_cta?: array{url: string, label: string}|null,
     *   note?: string,
     *   footer_note?: string
     * } $data
     */
    public static function render(array $data): string
    {
        $logoUrl = self::publicUrl('assets/img/logo.png');
        $siteUrl = self::publicUrl('/');
        $year = date('Y');

        $preheader = self::plain((string) ($data['preheader'] ?? ''));
        $eyebrow = self::plain((string) ($data['eyebrow'] ?? 'Mechanical Academy'));
        $title = (string) ($data['title'] ?? '');
        $greeting = (string) ($data['greeting'] ?? '');
        $body = (string) ($data['body'] ?? '');
        $note = (string) ($data['note'] ?? '');
        $footerNote = (string) ($data['footer_note'] ?? 'Este email foi enviado automaticamente pela Mechanical Academy. Se não esperava esta mensagem, pode ignorá-la.');
        $info = is_array($data['info'] ?? null) ? $data['info'] : [];
        $cta = is_array($data['cta'] ?? null) ? $data['cta'] : null;
        $secondaryCta = is_array($data['secondary_cta'] ?? null) ? $data['secondary_cta'] : null;

        $infoHtml = self::infoRows($info);
        $ctaHtml = self::ctaButton($cta);
        $secondaryHtml = self::textLink($secondaryCta);

        $greetingHtml = $greeting !== ''
            ? '<p style="margin:0 0 16px;font-size:16px;line-height:1.6;color:' . self::INK . ';">' . $greeting . '</p>'
            : '';

        $titleHtml = $title !== ''
            ? '<h1 style="margin:0 0 18px;font-size:24px;line-height:1.3;font-weight:700;color:' . self::INK . ';letter-spacing:-0.02em;">' . self::e($title) . '</h1>'
            : '';

        $noteHtml = $note !== ''
            ? '<p style="margin:22px 0 0;font-size:13px;line-height:1.55;color:' . self::MUTED . ';">' . $note . '</p>'
            : '';

        return '<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>' . self::e($title !== '' ? $title : 'Mechanical Academy') . '</title>
<!--[if mso]><noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:OfficeDocumentSettings></xml></noscript><![endif]-->
</head>
<body style="margin:0;padding:0;background:#e8ecf1;width:100% !important;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">
' . ($preheader !== '' ? '<div style="display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;">' . self::e($preheader) . '</div>' : '') . '
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#e8ecf1;">
<tr>
<td align="center" style="padding:28px 12px;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="width:100%;max-width:600px;border-collapse:separate;">

<!-- Header -->
<tr>
<td style="background:' . self::BG_DARK . ';border-radius:14px 14px 0 0;padding:0;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td style="padding:28px 32px 22px;background:linear-gradient(135deg, ' . self::BG_DARK . ' 0%, ' . self::SURFACE . ' 55%, #0a1628 100%);border-radius:14px 14px 0 0;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="left" style="vertical-align:middle;">
<a href="' . self::e($siteUrl) . '" style="text-decoration:none;">
<img src="' . self::e($logoUrl) . '" alt="Mechanical Academy" width="168" style="display:block;width:168px;max-width:168px;height:auto;border:0;outline:none;text-decoration:none;">
</a>
</td>
</tr>
<tr>
<td style="padding-top:18px;">
<div style="height:3px;width:56px;background:linear-gradient(90deg, ' . self::ACCENT . ', ' . self::ACCENT_SOFT . ');border-radius:3px;"></div>
<p style="margin:14px 0 0;font-family:Arial,Helvetica,sans-serif;font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:' . self::ACCENT_SOFT . ';font-weight:600;">' . self::e($eyebrow) . '</p>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>

<!-- Body -->
<tr>
<td style="background:' . self::CARD . ';padding:34px 32px 28px;font-family:Arial,Helvetica,sans-serif;">
' . $greetingHtml . '
' . $titleHtml . '
<div style="font-size:15px;line-height:1.7;color:' . self::MUTED . ';">' . $body . '</div>
' . $infoHtml . '
' . $ctaHtml . '
' . $secondaryHtml . '
' . $noteHtml . '
</td>
</tr>

<!-- Footer -->
<tr>
<td style="background:' . self::BG_DARK . ';border-radius:0 0 14px 14px;padding:22px 32px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td style="font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.55;color:#8b95a5;">
<strong style="color:#ffffff;">Mechanical Academy</strong><br>
' . self::e($footerNote) . '<br>
<span style="color:#5c6570;">&copy; ' . $year . ' Mechanical Tecnologia</span>
</td>
</tr>
<tr>
<td style="padding-top:14px;">
<a href="' . self::e($siteUrl) . '" style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:' . self::ACCENT_SOFT . ';text-decoration:none;">Abrir plataforma</a>
<span style="color:#3a4250;padding:0 8px;">·</span>
<a href="mailto:academy@mechanical.co.mz" style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:' . self::ACCENT_SOFT . ';text-decoration:none;">academy@mechanical.co.mz</a>
</td>
</tr>
</table>
</td>
</tr>

</table>
</td>
</tr>
</table>
</body>
</html>';
    }

    public static function p(string $html): string
    {
        return '<p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:' . self::MUTED . ';">' . $html . '</p>';
    }

    public static function strong(string $text): string
    {
        return '<strong style="color:' . self::INK . ';">' . self::e($text) . '</strong>';
    }

    /**
     * @param list<array{label: string, value: string}> $rows
     */
    public static function infoRows(array $rows): string
    {
        if ($rows === []) {
            return '';
        }

        $html = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:22px 0 8px;border:1px solid #e4e8ee;border-radius:10px;overflow:hidden;">';
        foreach ($rows as $i => $row) {
            $bg = ($i % 2 === 0) ? '#f7f9fc' : '#ffffff';
            $label = self::e((string) ($row['label'] ?? ''));
            $value = (string) ($row['value'] ?? '');
            $html .= '<tr>'
                . '<td style="padding:12px 16px;background:' . $bg . ';font-family:Arial,Helvetica,sans-serif;font-size:12px;color:' . self::MUTED . ';width:38%;vertical-align:top;">' . $label . '</td>'
                . '<td style="padding:12px 16px;background:' . $bg . ';font-family:Arial,Helvetica,sans-serif;font-size:14px;color:' . self::INK . ';font-weight:600;vertical-align:top;">' . $value . '</td>'
                . '</tr>';
        }
        $html .= '</table>';

        return $html;
    }

    /**
     * @param array{url?: string, label?: string}|null $cta
     */
    public static function ctaButton(?array $cta): string
    {
        $url = trim((string) ($cta['url'] ?? ''));
        $label = trim((string) ($cta['label'] ?? ''));
        if ($url === '' || $label === '') {
            return '';
        }

        return '<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:28px 0 8px;">
<tr>
<td align="center" bgcolor="' . self::ACCENT . '" style="border-radius:8px;background:' . self::ACCENT . ';">
<a href="' . self::e($url) . '" style="display:inline-block;padding:14px 28px;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:8px;background:' . self::ACCENT . ';">'
            . self::e($label) .
            '</a>
</td>
</tr>
</table>
<p style="margin:10px 0 0;font-size:12px;line-height:1.5;color:#8b95a5;word-break:break-all;">Se o botão não funcionar, use este link:<br><a href="' . self::e($url) . '" style="color:' . self::ACCENT . ';text-decoration:none;">' . self::e($url) . '</a></p>';
    }

    /**
     * @param array{url?: string, label?: string}|null $cta
     */
    public static function textLink(?array $cta): string
    {
        $url = trim((string) ($cta['url'] ?? ''));
        $label = trim((string) ($cta['label'] ?? ''));
        if ($url === '' || $label === '') {
            return '';
        }

        return '<p style="margin:16px 0 0;font-size:14px;line-height:1.6;color:' . self::MUTED . ';">'
            . '<a href="' . self::e($url) . '" style="color:' . self::ACCENT . ';font-weight:600;text-decoration:none;">' . self::e($label) . ' →</a>'
            . '</p>';
    }

    private static function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private static function plain(string $value): string
    {
        return trim(strip_tags($value));
    }

    /**
     * URL absoluta pública (logo/links nos emails).
     * Prefere app.baseURL do .env quando definido.
     */
    private static function publicUrl(string $path): string
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');
        $configured = trim((string) env('app.baseURL', ''));

        if ($configured !== '') {
            return rtrim($configured, '/') . ($path !== '' ? '/' . $path : '/');
        }

        if ($path === '' || $path === '/') {
            return site_url('/');
        }

        return base_url($path);
    }
}
