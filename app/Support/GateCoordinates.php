<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Deterministic synthetic geographic coordinates for airport gates.
 *
 * Gate codes follow the pattern <letter A–K><number 1–29> (e.g. "A12", "K3").
 * Each gate is mapped to a stable lat/lng offset around a neutral, *fictional*
 * coastal airport center so the same gate always lands on the same point.
 *
 * Location is deliberately NOT tied to any real-world named airport — these
 * coordinates are synthetic by design (see project rules: synthetic data only).
 *
 * Scheme:
 *   - letter (A=0 .. K=10) → latitude row, walking north as the letter increases
 *   - number (1..29)       → longitude column, walking east as the number increases
 *   - constant small spacing keeps the whole apron within a tight bounding box
 *     around the center, so a single sensible zoom shows every gate.
 */
final class GateCoordinates
{
    /**
     * Neutral, generic coastal airport center (fictional). Not a real airport.
     */
    public const CENTER_LAT = -6.1256;

    public const CENTER_LNG = 106.6559;

    /** Default Leaflet zoom level that frames the whole synthetic apron. */
    public const DEFAULT_ZOOM = 15;

    /** Gate rows A–K. */
    private const ROWS = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];

    /** Highest gate number on a row. */
    private const COLUMN_COUNT = 29;

    /** Degrees of latitude between adjacent rows. */
    private const ROW_SPACING = 0.0016;

    /** Degrees of longitude between adjacent gate numbers. */
    private const COL_SPACING = 0.0011;

    /**
     * Resolve a gate code (e.g. "A12") to deterministic [lat, lng].
     *
     * Returns null when the code does not parse to a valid row/column, so
     * callers can safely skip unknown/legacy location strings.
     *
     * @return array{lat: float, lng: float}|null
     */
    public static function forGate(string $code): ?array
    {
        if (! preg_match('/^([A-K])([0-9]{1,2})$/', strtoupper(trim($code)), $m)) {
            return null;
        }

        $rowIndex = array_search($m[1], self::ROWS, true);
        $number = (int) $m[2];

        if ($rowIndex === false || $number < 1 || $number > self::COLUMN_COUNT) {
            return null;
        }

        // Center the grid on the airport center: rows fan out around the middle
        // row (F = index 5) and columns around the middle gate (15).
        $rowOffset = ($rowIndex - (count(self::ROWS) - 1) / 2) * self::ROW_SPACING;
        $colOffset = ($number - (self::COLUMN_COUNT + 1) / 2) * self::COL_SPACING;

        return [
            'lat' => round(self::CENTER_LAT + $rowOffset, 6),
            'lng' => round(self::CENTER_LNG + $colOffset, 6),
        ];
    }
}
