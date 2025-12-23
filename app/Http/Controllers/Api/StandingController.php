<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StandingService;
use Illuminate\Http\JsonResponse;

class StandingController extends Controller
{
    public function __construct( private StandingService $standingService){}

    public function index(): JsonResponse {
        $standings = $this->standingService->getStandings();
        return response()->json(['standings' => $standings]);
    }
}
