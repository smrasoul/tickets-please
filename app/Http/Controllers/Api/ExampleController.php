<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExampleController extends Controller
{


    /**
     * @OA\Get(
     *     path="/api/example",
     *     summary="Returns a greeting message",
     *     tags={"Example"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response"
     *     )
     * )
     */
    public function index()
    {
        return response()->json(['message' => 'Hello from Swagger!']);
    }
}
