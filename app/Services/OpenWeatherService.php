<?php

namespace App\Services;

use App\DTOS\WeatherDTO;
use App\Http\Requests\SearchForecastRequest;
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * [Description OpenWeatherService]
 */
class OpenWeatherService
{
    const OPEN_API_URL = 'http://api.openweathermap.org';

    public function __construct(
        private readonly GuzzleClient $guzzleClient,
        private readonly ForecastsService $forecastService,
    )
    {
    }

    /**
     * @param string $query
     * 
     * @return Collection
     */
    public function searchForecastByLocation(SearchForecastRequest $searchForecastRequest): array
    {
        $request = new GuzzleRequest(method: 'GET',
            uri: sprintf(
                '%s/data/2.5/forecast?q=%s&units=metric&appid=%s',
                self::OPEN_API_URL,
                $searchForecastRequest->get('location'),
                config('services.openweather.appid')
            )
        );

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ];

        $response = $this->guzzleClient->send($request, $options);

        $responseBody = json_decode($response->getBody()->getContents(), true);

        if (isset($responseBody['cod']) && $responseBody['cod'] === '200') {
            $forecasts = $this->formatResponse($responseBody['list'], $searchForecastRequest->get('location'));

            return $this->forecastService->saveForecasts(
                forecasts: $forecasts,
                location: $searchForecastRequest->get('location'),
                user_id: $searchForecastRequest->user()->id,
            );
        }

        return [];
    }

    private function formatResponse(array $forecasts, string $query): array
    {
        $collection_forecasts = new Collection($forecasts);

        $forecasts_grouped_by_date =  $collection_forecasts->mapToGroups(
            fn ($weather) => [Carbon::parse($weather['dt_txt'])->toDateString() => $weather]
        );

        $forecasts = [];
        
        foreach($forecasts_grouped_by_date as $key => $values) {
            $collection_grouped_forecast = (new Collection($values))->map(
                fn ($weather) => new WeatherDTO(
                    temp: $weather['main']['temp'] ?? null,
                    temp_min: $weather['main']['temp_min'] ?? null,
                    temp_max: $weather['main']['temp_max'] ?? null,
                    weather: $weather['weather'][0]['main'] ?? null,
                    weather_description: Str::title($weather['weather'][0]['description']) ?? null,
                    weather_icon: $weather['weather'][0]['icon'] ?? null,
                    date_txt: $weather['dt_txt'],
                    date_timestamp: $weather['dt'],
                )
            );

            $first_weather = $collection_grouped_forecast->first();

            $forecasts[] = [
                'location' => $query,
                'date_timestamp' => $first_weather->date_timestamp,
                'date_txt' => $key,
                'temp_min' => $first_weather->temp_min,
                'temp_max' => $first_weather->temp_max,
                'weather' => $first_weather->weather,
                'weather_description' => $first_weather->weather_description,
                'weather_icon' => $first_weather->weather_icon,
                'forecasts' => $collection_grouped_forecast->toArray(),
            ];
        }

        return $forecasts;
    }
}