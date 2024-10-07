<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchForecastRequest;
use App\Repositories\ForecastRepository;
use App\Services\ForecastsService;
use App\Services\OpenWeatherService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ForecastsController extends Controller
{
    /**
     * Get existing forecasts for current user
     * 
     * @OA\Get(
     *    path="/forecasts",
     *    tags={"forecasts"},
     *    summary="Get existing forecasts for current user",
     *    description="Get existing forecasts for current user",
     *    operationId="getUserForecasts",
     *    @OA\Response(response=200, description="successful operation"),
     *    @OA\Response(response=400, description="An error happened"),
     *  )
     * 
     * @param Request $request
     * @param ForecastRepository $forecastRepository
     * 
     * @return JsonResponse
     */
    public function getUserForecasts(
        Request $request,
        ForecastRepository $forecastRepository,
        ForecastsService $forecastsService,
    ): JsonResponse
    {
        try {
            $forecasts = $forecastRepository->getUserForecastsGroupedByLocation($request->user()->id);

            if (empty($forecasts->toArray())) {
                return new JsonResponse([]);
            }

            $forecasts_return = [];

            foreach ($forecasts as $key => $values) {
                $forecasts_return[$key] = $forecastsService->formatReturn(new Collection($values));
            }

            return new JsonResponse(array_values($forecasts_return));
        } catch (Exception $ex) {
            return new JsonResponse(
                [
                    'error_message' => $ex->getMessage(),
                    'error_code' => $ex->getCode()
                ],
                400
            );
        }
    }

    /**
     * Search forecast by a location string
     * 
     * @OA\Post(
     *    path="/forecasts",
     *    tags={"forecasts-search"},
     *    summary="Search forecast by a location string",
     *    description="Search forecast by a location string",
     *    operationId="searchForecast",
     *    @OA\Response(response=200, description="successful operation"),
     *    @OA\Response(response=400, description="An error happened"),
     *    @OA\Response(response=422, description="Invalid Search information"),
     *  )
     * 
     * @param SearchForecastRequest $searchForecastRequest
     * @param ForecastRepository $forecastRepository
     * @param OpenWeatherService $openWeatherService
     * 
     * @return JsonResponse
     */
    public function searchForecast(
        SearchForecastRequest $searchForecastRequest,
        ForecastRepository $forecastRepository,
        OpenWeatherService $openWeatherService,
        ForecastsService $forecastsService,
    ): JsonResponse
    {
        try {
            $forecasts = $forecastRepository->getByUserAndLocation(
                user_id: $searchForecastRequest->user()->id,
                location: $searchForecastRequest->get('location')
            );

            if (!empty($forecasts->toArray())) {
                $first_weather = $forecasts->first();

                // If last than 3 days since it was saved, we just return it.
                if ((Carbon::now())->diffInDays(Carbon::parse($first_weather?->date_txt)) < 3) {
                    return new JsonResponse(
                        $forecastsService->formatReturn($forecasts)
                    );
                }
            }

            $forecasts = $forecastRepository->getUserForecastsGroupedByLocation($searchForecastRequest->user()->id);

            if ($forecasts->count() >= 3) {
                return new JsonResponse([
                    'message' => 'All locations slots filled. Please, remove one location to be able to search again.'
                ], 400);
            }

            // If no forecasts already saved or more than 3 days since it was saved or less than 3 locations saved, we search again
            $forecasts = $openWeatherService->searchForecastByLocation($searchForecastRequest);

            return new JsonResponse(
                $forecastsService->formatReturn(new Collection($forecasts))
            );
        } catch (Exception $ex) {
            return new JsonResponse(
                [
                    'error_message' => $ex->getMessage(),
                    'error_code' => $ex->getCode(),
                    'error_trace' => $ex->getTrace(),
                ],
                400
            );
        }
    }

    /**
     * Delete a forecast by a location string
     * 
     * @OA\Delete(
     *    path="/forecasts/{location}",
     *    tags={"forecasts-delete"},
     *    summary="Delete a forecast by a location string",
     *    description="Delete a forecast by a location string",
     *    operationId="removeForecast",
     *    @OA\Response(response=200, description="successful operation"),
     *    @OA\Response(response=400, description="An error happened"),
     *  )
     * 
     * @param Request $request
     * @param ForecastsService $forecastsService
     * 
     * @return JsonResponse
     */
    public function removeForecast(
        Request $request,
        string $location,
        ForecastsService $forecastsService,
    ): JsonResponse
    {
        try {
            $forecastsService->removePreviousForecastsForLocation(
                location: $location,
                user_id: $request->user()->id,
            );

            return new JsonResponse([]);
        } catch (Exception $ex) {
            return new JsonResponse(
                [
                    'error_message' => $ex->getMessage(),
                    'error_code' => $ex->getCode(),
                    'error_trace' => $ex->getTrace(),
                ],
                400
            );
        }
    }
}