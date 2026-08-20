<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class HolidayService
{
    /**
     * @param  array<string, array<int, string>>  $overrideHolidaysByYear  Fechas forzadas por año (pruebas/offline)
     * @param  int  $cacheMinutes  TTL del caché por año
     */
    public function __construct(
        private array $overrideHolidaysByYear = [],
        private int $cacheMinutes = 43200
    ) {}

    /**
     * Determina si una fecha es festivo en el país configurado, según el año de esa fecha.
     */
    public function isHoliday(\DateTimeInterface|string $date): bool
    {
        $date = $date instanceof \DateTimeInterface ? $date : Carbon::parse($date);
        $year = (int) $date->format('Y');
        $key = $date->format('m-d');

        return in_array($key, $this->holidaysForYear($year), true);
    }

    /**
     * Obtiene los festivos de un año (mes-día). Fuentes en orden de prioridad:
     * 1. Override inyectado (pruebas/offline)
     * 2. Caché de Laravel
     * 3. API pública Nager.Date ({config holidays.country_code})
     * 4. Fechas manuales en config/holidays.php (fallback offline)
     */
    public function holidaysForYear(int $year): array
    {
        $yearKey = (string) $year;

        if (! empty($this->overrideHolidaysByYear[$yearKey])) {
            return $this->normalize($this->overrideHolidaysByYear[$yearKey]);
        }

        return Cache::remember(
            "holidays.{$this->countryCode()}.{$year}",
            $this->cacheMinutes,
            fn () => $this->fetchYear($year)
        );
    }

    private function countryCode(): string
    {
        return strtolower((string) config('holidays.country_code', 'CO'));
    }

    /**
     * Convierte fechas completas (Y-m-d) a claves mes-día (m-d).
     */
    private function normalize(array $dates): array
    {
        $normalized = [];

        foreach ($dates as $date) {
            $parsed = Carbon::parse($date);
            $normalized[] = $parsed->format('m-d');
        }

        return array_values(array_unique($normalized));
    }

    private function fetchYear(int $year): array
    {
        try {
            $response = Http::timeout(10)
                ->get("https://date.nager.at/api/v3/PublicHolidays/{$year}/{$this->countryCode()}");

            if ($response->ok()) {
                $data = $response->json();

                if (is_array($data)) {
                    $dates = array_values(array_filter(
                        array_column($data, 'date'),
                        fn ($date) => is_string($date) && str_contains($date, '-')
                    ));

                    if (count($dates) > 0) {
                        return $this->normalize($dates);
                    }
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $this->manualDatesForYear($year);
    }

    /**
     * Fallback offline: fechas manuales definidas en config/holidays.php.
     */
    private function manualDatesForYear(int $year): array
    {
        $dates = collect((array) config('holidays.manual_dates', []))
            ->filter(fn ($date) => is_string($date) && str_starts_with($date, (string) $year))
            ->values()
            ->all();

        return $this->normalize($dates);
    }
}
