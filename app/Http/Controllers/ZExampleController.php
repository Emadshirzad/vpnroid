<?php

namespace App\Http\Controllers;

class ZExampleController extends Controller
{
    /**
    * @OA\Get(
    *     path="/test",
    *     tags={"Test"},
    *     summary="my info",
    *     description="my info",
    *     @OA\Response(
    *         response=200,
    *         description="Success Message",
    *         @OA\JsonContent(ref="#/components/schemas/SuccessModel"),
    *     ),
    *     @OA\Response(
    *         response=400,
    *         description="an 'unexpected' error",
    *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
    *     )
    * )
    * Get the authenticated User.
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function test()
    {
        return response()->json(['message' => 'hello world']);
    }
}
