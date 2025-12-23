<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PredictionService;
use Illuminate\Http\JsonResponse;

class PredictionController extends Controller
{
    public function __construct(private PredictionService $predictionService){}
    public function index(): JsonResponse{
        $predictions = $this->predictionService->getChampionshipOdds();

        return response()->json([
            'predictions' => $predictions
        ]);
    }
}