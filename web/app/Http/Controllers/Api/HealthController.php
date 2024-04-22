<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;

class HealthController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/api/health",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *
     *     @OA\PathItem (
     *     ),
     * )
     */
    public function index()
    {
        return response()->json([
            'status' => 'ok',
            'message' => 'API is running.',
        ]);

    }
}
