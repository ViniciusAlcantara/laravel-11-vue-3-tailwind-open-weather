<?php

namespace App\DTOS;

class WeatherDTO
{
    public function __construct(
        public ?float  $temp,
        public ?float  $temp_max,
        public ?float  $temp_min,
        public ?string $weather,
        public ?string $weather_description,
        public ?string $weather_icon,
        public ?string $date_txt,
        public ?string $date_timestamp,
    )
    {
    }

    public function toArray(): array
    {
        $array = [
            'temp' => $this->temp,
            'temp_max' => $this->temp_max,
            'temp_min' => $this->temp_min,
            'weather' => $this->weather,
            'weather_description' => $this->weather_description,
            'weather_icon' => $this->weather_icon,
            'date_txt' => $this->date_txt,
            'date_timestamp' => $this->date_timestamp,
        ];

        // Remove null values
        return array_filter($array, function ($value) {
            return !is_null($value);
        });
    }
}
