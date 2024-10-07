<?php

namespace App\Services;

use App\Models\Forecast;
use App\Repositories\ForecastRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * [Description OpenWeatherService]
 */
class ForecastsService
{
    public function __construct(private readonly ForecastRepository $forecastRepository)
    {
        
    }

    /**
     * @param array $forecasts
     * @param string $location
     * @param int $user_id
     * 
     * @return void
     */
    public function saveForecasts(array $forecasts, string $location, int $user_id): array
    {
        // Removes previous forecasts with same location, prior adding a new one
        $this->removePreviousForecastsForLocation($location, $user_id);

        $saved_forecasts = [];
        foreach($forecasts as $forecast) {
            $saved_forecasts[] = Forecast::create([
                'user_id' => $user_id,
                'location' => $location,
                'date_timestamp' => $forecast['date_timestamp'],
                'date_txt' => $forecast['date_txt'],
                'temp_min' => $forecast['temp_min'],
                'temp_max' => $forecast['temp_max'],
                'weather' => $forecast['weather'],
                'weather_description' => $forecast['weather_description'],
                'weather_icon' => $forecast['weather_icon'],
                'forecasts' => json_encode($forecast['forecasts']),
            ]);
        }

        return $saved_forecasts;
    }

    /**
     * @param string $location
     * @param int $user_id
     * 
     * @return void
     */
    public function removePreviousForecastsForLocation(string $location, int $user_id): void
    {
        $forecasts = $this->forecastRepository->getByUserAndLocation(
            user_id: $user_id,
            location: $location
        );

        $forecasts->map(fn ($forecast) => $forecast->delete());
    }

    /**
     * @param Collection $forecasts
     * 
     * @return array
     */
    public function formatReturn(Collection $forecasts): array
    {
        $first_weather = $forecasts->first();

        return [
            ...$first_weather->toArray(),
            'forecasts' => json_decode($first_weather->forecasts),
            'other_days_forecasts' => $forecasts->skip(1)->map(fn ($forecast) => [
                ...$forecast->toArray(),
                'date_front' => Carbon::parse($forecast->date_txt)->isoFormat('ddd Do, MMM YY'),
                'forecasts' => json_decode($forecast->forecasts)
            ])
        ];
    }
}