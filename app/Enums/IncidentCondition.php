<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * The allowed casualty conditions for an incident (legacy values).
 */
enum IncidentCondition: string
{
    case Meninggal = 'meninggal';
    case Ringan = 'ringan';
    case Sedang = 'sedang';
    case Berat = 'berat';

    /**
     * All condition values as a flat list (for validation / select options).
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $case): string => $case->value, self::cases());
    }

    /**
     * Human-friendly Indonesian label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Meninggal => 'Meninggal',
            self::Ringan => 'Luka Ringan',
            self::Sedang => 'Luka Sedang',
            self::Berat => 'Luka Berat',
        };
    }

    /**
     * Tailwind color token used for charts/badges, keyed by value.
     *
     * @return array<string, string>
     */
    public static function chartColors(): array
    {
        return [
            self::Meninggal->value => '#dc2626', // red-600
            self::Berat->value => '#ea580c',     // orange-600
            self::Sedang->value => '#ca8a04',    // yellow-600
            self::Ringan->value => '#16a34a',    // green-600
        ];
    }

    /**
     * Tailwind utility classes for a status badge (bg + text + ring),
     * dark-mode aware. Used by the incidents table.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Meninggal => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-300 dark:ring-red-500/30',
            self::Berat => 'bg-orange-50 text-orange-700 ring-orange-600/20 dark:bg-orange-500/10 dark:text-orange-300 dark:ring-orange-500/30',
            self::Sedang => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-500/30',
            self::Ringan => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/30',
        };
    }

    /**
     * Resolve a (possibly legacy / unknown) stored value to an enum case,
     * falling back to null when it does not map cleanly.
     */
    public static function tryFromValue(?string $value): ?self
    {
        return $value === null ? null : self::tryFrom($value);
    }
}
