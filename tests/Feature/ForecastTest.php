<?php

use App\Models\User;
use App\Repositories\ForecastRepository;
use App\Services\ForecastsService;
use App\Services\OpenWeatherService;

beforeEach(function () {
    $this->forecastRepository = Mockery::mock(ForecastRepository::class);
    $this->forecastsService = Mockery::mock(ForecastsService::class);
    $this->openWeatherService = Mockery::mock(OpenWeatherService::class);
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('it should return user forecasts', function () {
    // Arrange
    $forecasts = collect([
        'Belo Horizonte, Brazil' => [
            [
                "id" => 1,
                "location" => "Belo Horizonte, Brazil",
                "date_timestamp" => 1728270000,
                "date_txt" => "2024-10-07",
                "temp_max" => 22.17,
                "temp_min" => 20.3,
                "weather" => "Clouds",
                "weather_description" => "Few Clouds",
                "weather_icon" => "02n",
                "forecasts" => json_encode([
                    ["temp"=>22.17,"temp_max"=>22.17,"temp_min"=>20.3,"weather"=>"Clouds","weather_description"=>"Few Clouds","weather_icon"=>"02n","date_txt"=>"2024-10-07 03=>00=>00","date_timestamp"=>"1728270000"],
                    ["temp"=>20.25,"temp_max"=>20.25,"temp_min"=>18.83,"weather"=>"Clouds","weather_description"=>"Few Clouds","weather_icon"=>"02n","date_txt"=>"2024-10-07 06=>00=>00","date_timestamp"=>"1728280800"],
                    ["temp"=>17.69,"temp_max"=>17.69,"temp_min"=>17.69,"weather"=>"Clouds","weather_description"=>"Scattered Clouds","weather_icon"=>"03d","date_txt"=>"2024-10-07 09=>00=>00","date_timestamp"=>"1728291600"],
                    ["temp"=>26.39,"temp_max"=>26.39,"temp_min"=>26.39,"weather"=>"Clouds","weather_description"=>"Scattered Clouds","weather_icon"=>"03d","date_txt"=>"2024-10-07 12=>00=>00","date_timestamp"=>"1728302400"],
                    ["temp"=>31.98,"temp_max"=>31.98,"temp_min"=>31.98,"weather"=>"Clouds","weather_description"=>"Scattered Clouds","weather_icon"=>"03d","date_txt"=>"2024-10-07 15=>00=>00","date_timestamp"=>"1728313200"],
                    ["temp"=>34.05,"temp_max"=>34.05,"temp_min"=>34.05,"weather"=>"Clouds","weather_description"=>"Scattered Clouds","weather_icon"=>"03d","date_txt"=>"2024-10-07 18=>00=>00","date_timestamp"=>"1728324000"],
                    ["temp"=>29.08,"temp_max"=>29.08,"temp_min"=>29.08,"weather"=>"Clouds","weather_description"=>"Scattered Clouds","weather_icon"=>"03n","date_txt"=>"2024-10-07 21=>00=>00","date_timestamp"=>"1728334800"]
                ]),
                "user_id" => 1,
                "created_at" => "2024-10-07 01=>02=>30",
                "updated_at" => "2024-10-07 01=>02=>30",
            ],
            [
                "id" => 1,
                "location" => "Belo Horizonte, Brazil",
                "date_timestamp" => 1728270000,
                "date_txt" => "2024-10-07",
                "temp_max" => 22.17,
                "temp_min" => 20.3,
                "weather" => "Clouds",
                "weather_description" => "Few Clouds",
                "weather_icon" => "02n",
                "forecasts" => json_encode([
                    ["temp"=>22.17,"temp_max"=>22.17,"temp_min"=>20.3,"weather"=>"Clouds","weather_description"=>"Few Clouds","weather_icon"=>"02n","date_txt"=>"2024-10-07 03=>00=>00","date_timestamp"=>"1728270000"],
                    ["temp"=>20.25,"temp_max"=>20.25,"temp_min"=>18.83,"weather"=>"Clouds","weather_description"=>"Few Clouds","weather_icon"=>"02n","date_txt"=>"2024-10-07 06=>00=>00","date_timestamp"=>"1728280800"],
                    ["temp"=>17.69,"temp_max"=>17.69,"temp_min"=>17.69,"weather"=>"Clouds","weather_description"=>"Scattered Clouds","weather_icon"=>"03d","date_txt"=>"2024-10-07 09=>00=>00","date_timestamp"=>"1728291600"],
                    ["temp"=>26.39,"temp_max"=>26.39,"temp_min"=>26.39,"weather"=>"Clouds","weather_description"=>"Scattered Clouds","weather_icon"=>"03d","date_txt"=>"2024-10-07 12=>00=>00","date_timestamp"=>"1728302400"],
                    ["temp"=>31.98,"temp_max"=>31.98,"temp_min"=>31.98,"weather"=>"Clouds","weather_description"=>"Scattered Clouds","weather_icon"=>"03d","date_txt"=>"2024-10-07 15=>00=>00","date_timestamp"=>"1728313200"],
                    ["temp"=>34.05,"temp_max"=>34.05,"temp_min"=>34.05,"weather"=>"Clouds","weather_description"=>"Scattered Clouds","weather_icon"=>"03d","date_txt"=>"2024-10-07 18=>00=>00","date_timestamp"=>"1728324000"],
                    ["temp"=>29.08,"temp_max"=>29.08,"temp_min"=>29.08,"weather"=>"Clouds","weather_description"=>"Scattered Clouds","weather_icon"=>"03n","date_txt"=>"2024-10-07 21=>00=>00","date_timestamp"=>"1728334800"]
                ]),
                "user_id" => 1,
                "created_at" => "2024-10-07 01=>02=>30",
                "updated_at" => "2024-10-07 01=>02=>30",
            ]
        ]
    ]);

    $this->forecastRepository
        ->shouldReceive('getUserForecastsGroupedByLocation')
        ->once()
        ->with($this->user->id)
        ->andReturn($forecasts);

    $this->forecastsService
        ->shouldReceive('formatReturn')
        ->once()
        ->andReturn($forecasts->first());

    $this->app->instance(ForecastRepository::class, $this->forecastRepository);
    $this->app->instance(ForecastsService::class, $this->forecastsService);

    // Act
    $response = $this->getJson('/forecasts');

    // Assert
    $response->assertStatus(200);
    $response->assertJsonPath('0.0.weather', 'Clouds');
    $response->assertJsonPath('0.0.weather_description', 'Few Clouds');
    $response->assertJsonPath('0.0.weather_icon', '02n');
    $response->assertJsonPath('0.0.temp_max', 22.17);
    $response->assertJsonPath('0.0.temp_min', 20.3);
    $response->assertJsonPath('0.1.weather', 'Clouds');
});

test('it should search forecast by location', function () {
    // Arrange
    $forecasts = collect([
        [
            "id" => 1,
            "location" => "New York",
            "date_timestamp" => 1728270000,
            "date_txt" => "2024-10-07",
            "temp_max" => 22.17,
            "temp_min" => 20.3,
            "weather" => "Clouds",
            "weather_description" => "Few Clouds",
            "weather_icon" => "02n",
            "forecasts" => json_encode([
                ["temp"=>22.17,"temp_max"=>22.17,"temp_min"=>20.3,"weather"=>"Clouds","weather_description"=>"Few Clouds","weather_icon"=>"02n","date_txt"=>"2024-10-07 03=>00=>00","date_timestamp"=>"1728270000"],
                ["temp"=>20.25,"temp_max"=>20.25,"temp_min"=>18.83,"weather"=>"Clouds","weather_description"=>"Few Clouds","weather_icon"=>"02n","date_txt"=>"2024-10-07 06=>00=>00","date_timestamp"=>"1728280800"],
                ["temp"=>17.69,"temp_max"=>17.69,"temp_min"=>17.69,"weather"=>"Clouds","weather_description"=>"Scattered Clouds","weather_icon"=>"03d","date_txt"=>"2024-10-07 09=>00=>00","date_timestamp"=>"1728291600"],
                ["temp"=>26.39,"temp_max"=>26.39,"temp_min"=>26.39,"weather"=>"Clouds","weather_description"=>"Scattered Clouds","weather_icon"=>"03d","date_txt"=>"2024-10-07 12=>00=>00","date_timestamp"=>"1728302400"],
                ["temp"=>31.98,"temp_max"=>31.98,"temp_min"=>31.98,"weather"=>"Clouds","weather_description"=>"Scattered Clouds","weather_icon"=>"03d","date_txt"=>"2024-10-07 15=>00=>00","date_timestamp"=>"1728313200"],
                ["temp"=>34.05,"temp_max"=>34.05,"temp_min"=>34.05,"weather"=>"Clouds","weather_description"=>"Scattered Clouds","weather_icon"=>"03d","date_txt"=>"2024-10-07 18=>00=>00","date_timestamp"=>"1728324000"],
                ["temp"=>29.08,"temp_max"=>29.08,"temp_min"=>29.08,"weather"=>"Clouds","weather_description"=>"Scattered Clouds","weather_icon"=>"03n","date_txt"=>"2024-10-07 21=>00=>00","date_timestamp"=>"1728334800"]
            ]),
            "user_id" => 1,
            "created_at" => "2024-10-07 01=>02=>30",
            "updated_at" => "2024-10-07 01=>02=>30",
        ],
    ]);

    $this->forecastRepository
        ->shouldReceive('getByUserAndLocation')
        ->once()
        ->with($this->user->id, 'New York')
        ->andReturn(collect());

    $this->openWeatherService
        ->shouldReceive('searchForecastByLocation')
        ->once()
        ->andReturn($forecasts->toArray());

    $this->forecastRepository
        ->shouldReceive('getUserForecastsGroupedByLocation')
        ->once()
        ->with($this->user->id)
        ->andReturn($forecasts);

    $this->forecastsService
        ->shouldReceive('formatReturn')
        ->once()
        ->andReturn($forecasts->toArray());

    $this->app->instance(ForecastRepository::class, $this->forecastRepository);
    $this->app->instance(OpenWeatherService::class, $this->openWeatherService);
    $this->app->instance(ForecastsService::class, $this->forecastsService);

    // Act
    $response = $this->postJson('/forecasts', [
        'location' => 'New York',
    ]);

    // Assert
    $response->assertStatus(200);
    $response->assertJsonPath('0.weather', 'Clouds');
    $response->assertJsonPath('0.weather_description', 'Few Clouds');
    $response->assertJsonPath('0.weather_icon', '02n');
    $response->assertJsonPath('0.temp_max', 22.17);
    $response->assertJsonPath('0.temp_min', 20.3);
});

test('it should remove forecast by location', function () {
    // Arrange
    $this->forecastsService
        ->shouldReceive('removePreviousForecastsForLocation')
        ->once()
        ->with('Los Angeles', $this->user->id);

    $this->app->instance(ForecastsService::class, $this->forecastsService);

    // Act
    $response = $this->deleteJson('/forecasts/Los Angeles');

    // Assert
    $response->assertStatus(200);
    $response->assertJson([]);
});

afterEach(function () {
    Mockery::close();
});
