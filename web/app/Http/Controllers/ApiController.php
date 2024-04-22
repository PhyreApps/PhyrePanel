<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="PhyrePanel - API Documentation",
 *     version="0.1",
 *
 *      @OA\Contact(
 *          email="info@phyrepanel.com"
 *      ),
 * )
 */
class ApiController extends BaseController
{
    //  use AuthorizesRequests, ValidatesRequests;
    use ValidatesRequests;
}
