<?php

namespace App\Services;

/**
 * Helpers for effective course price, hours and trial access.
 */
class CourseCommerceService
{
    public const DEFAULT_WHATSAPP = '258842726761';

    /**
     * @param object|array $course
     */
    public function getListPrice($course): float
    {
        return (float) $this->field($course, 'price_course', 0);
    }

    /**
     * @param object|array $course
     */
    public function getPromoPrice($course): ?float
    {
        $promo = $this->field($course, 'promo_price_course', null);
        if ($promo === null || $promo === '') {
            return null;
        }
        $value = (float) $promo;
        if ($value <= 0) {
            return null;
        }
        $list = $this->getListPrice($course);
        if ($list > 0 && $value >= $list) {
            return null;
        }
        if (! $this->isPromoStillValid($course)) {
            return null;
        }
        return $value;
    }

    /**
     * @param object|array $course
     */
    public function getPromoEndsAt($course): ?string
    {
        $ends = $this->field($course, 'promo_ends_at_course', null);
        if ($ends === null || $ends === '') {
            return null;
        }
        return (string) $ends;
    }

    /**
     * @param object|array $course
     */
    public function isPromoStillValid($course): bool
    {
        $ends = $this->getPromoEndsAt($course);
        if ($ends === null) {
            // Sem data = promoção activa enquanto houver preço promo
            return true;
        }
        $ts = strtotime($ends);
        return $ts !== false && $ts > time();
    }

    /**
     * @param object|array $course
     */
    public function getPromoRemainingSeconds($course): int
    {
        if (! $this->hasPromo($course)) {
            return 0;
        }
        $ends = $this->getPromoEndsAt($course);
        if ($ends === null) {
            return 0;
        }
        return max(0, strtotime($ends) - time());
    }

    /**
     * @param object|array $course
     */
    public function getEffectivePrice($course): float
    {
        $promo = $this->getPromoPrice($course);
        return $promo !== null ? $promo : $this->getListPrice($course);
    }

    /**
     * @param object|array $course
     */
    public function hasPromo($course): bool
    {
        return $this->getPromoPrice($course) !== null;
    }

    /**
     * @param object|array $course
     */
    public function getDiscountPercent($course): int
    {
        $list = $this->getListPrice($course);
        $promo = $this->getPromoPrice($course);
        if ($list <= 0 || $promo === null) {
            return 0;
        }
        return (int) round((($list - $promo) / $list) * 100);
    }

    private function field($course, string $key, $default = null)
    {
        $aliases = [
            'promo_price_course' => ['promo_price_course'],
            'promo_ends_at_course' => ['promo_ends_at_course'],
            'hours_mode_course' => ['hours_mode_course'],
            'hours_manual_course' => ['hours_manual_course', 'hours_course'],
            'free_lessons_count_course' => ['free_lessons_count_course', 'free_lessons_course'],
            'whatsapp_contact_course' => ['whatsapp_contact_course', 'whatsapp_course'],
        ];

        $keys = $aliases[$key] ?? [$key];
        foreach ($keys as $candidate) {
            if (is_array($course) && array_key_exists($candidate, $course) && $course[$candidate] !== null && $course[$candidate] !== '') {
                return $course[$candidate];
            }
            if (is_object($course) && isset($course->{$candidate}) && $course->{$candidate} !== null && $course->{$candidate} !== '') {
                return $course->{$candidate};
            }
        }

        if (is_array($course)) {
            return $course[$key] ?? $default;
        }
        return $course->{$key} ?? $default;
    }

    /**
     * @param object|array $course
     */
    public function resolveHoursLabel($course, int $autoMinutes): string
    {
        $mode = strtolower((string) $this->field($course, 'hours_mode_course', 'auto'));
        // Compatibilidade: hours_manual_course=1 (flag) + hours_course valor, de migration alternativa.
        $manualFlag = (int) (is_array($course)
            ? ($course['hours_manual_course'] ?? 0)
            : ($course->hours_manual_course ?? 0));
        $legacyHours = is_array($course) ? ($course['hours_course'] ?? null) : ($course->hours_course ?? null);

        if ($mode === 'manual' || ($manualFlag === 1 && $legacyHours !== null)) {
            $manual = (float) ($legacyHours ?? $this->field($course, 'hours_manual_course', 0));
            return $this->formatHours($manual);
        }
        return $this->formatHours($autoMinutes / 60);
    }

    /**
     * @param object|array $course
     */
    public function resolveHoursValue($course, int $autoMinutes): float
    {
        $mode = strtolower((string) $this->field($course, 'hours_mode_course', 'auto'));
        $manualFlag = (int) (is_array($course)
            ? ($course['hours_manual_course'] ?? 0)
            : ($course->hours_manual_course ?? 0));
        $legacyHours = is_array($course) ? ($course['hours_course'] ?? null) : ($course->hours_course ?? null);

        if ($mode === 'manual' || ($manualFlag === 1 && $legacyHours !== null)) {
            return max(0, (float) ($legacyHours ?? $this->field($course, 'hours_manual_course', 0)));
        }
        return max(0, $autoMinutes / 60);
    }

    public function formatHours(float $hours): string
    {
        if ($hours <= 0) {
            return '0 Horas';
        }
        $formatted = fmod($hours, 1.0) === 0.0
            ? number_format($hours, 0, ',', '.')
            : number_format($hours, 1, ',', '.');
        $unit = abs($hours - 1.0) < 0.00001 ? 'Hora' : 'Horas';
        return $formatted . ' ' . $unit;
    }

    /**
     * @param object|array $course
     */
    public function getFreeLessonsCount($course): int
    {
        return max(0, (int) $this->field($course, 'free_lessons_count_course', 0));
    }

    /**
     * @param object|array $course
     */
    public function getWhatsappNumber($course = null): string
    {
        $custom = $course ? trim((string) $this->field($course, 'whatsapp_contact_course', '')) : '';
        $digits = preg_replace('/\D+/', '', $custom !== '' ? $custom : self::DEFAULT_WHATSAPP);
        return $digits !== '' ? $digits : self::DEFAULT_WHATSAPP;
    }

    /**
     * @param object|array $course
     */
    public function buildWhatsappUrl($course, string $message): string
    {
        $phone = $this->getWhatsappNumber($course);
        return 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
    }
}
