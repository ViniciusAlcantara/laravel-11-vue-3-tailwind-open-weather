<?php

namespace App\Repositories;

use App\Models\Forecast;
use Illuminate\Support\Collection;

/**
 * Class PromptRepository
 *
 * Repository for Prompt Model
 */
class ForecastRepository
{
    /**
     * @param int $user_id
     * 
     * @return Collection
     */
    public function getUserForecastsGroupedByLocation(int $user_id): Collection
    {
        return Forecast::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('location');
    }

    /**
     * @param int $user_id
     * @param mixed $location
     * 
     * @return Collection
     */
    public function getByUserAndLocation(int $user_id, $location): Collection
    {
        return Forecast::where('user_id', $user_id)
            ->where('location', $location)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
