<?php

namespace App\Http\Controllers;

use App\Models\Envoy;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnvoyController extends Controller
{
    /**
     * @OA\Post(
     *     path="/auth/envoy/register-user",
     *     tags={"Envoy"},
     *     summary="register user by envoy",
     *     description="register envoy",
     *     operationId="register envoy",
     *     @OA\RequestBody(
     *         description="tasks input",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="username",
     *                 type="string",
     *                 description="username ",
     *                 example="emad"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 description="password",
     *                 example="password"
     *             ),
     *             @OA\Property(
     *                 property="password_confirmation",
     *                 type="string",
     *                 description="password confirm",
     *                 example="password"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success Message",
     *         @OA\JsonContent(ref="#/components/schemas/EnvoyModel"),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="an 'unexpected' error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *     ),security={{"api_key": {}}}
     * )
     *
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerUser(Request $request)
    {
        $data = $request->validate([
            'username'  => 'required|max:255',
            'password'  => 'required|confirmed|min:6',
        ]);
        try {
            $token = Auth::user();
            $user = User::create([
                'username' => $data['username'],
                'password' => $data['password'],
                'envoy_id' => $token->id
            ]);
            return $this->success($user);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->error($th->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/auth/envoy/envoy-users",
     *     tags={"Envoy"},
     *     summary="users envoy list",
     *     description="envoy info",
     *     @OA\Response(
     *         response=200,
     *         description="Success Message",
     *         @OA\JsonContent(ref="#/components/schemas/UserModel"),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="an 'unexpected' error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *     ),security={{"api_key": {}}}
     * )
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        try {
            $token = Auth::user();
            $subUser = User::whereEnvoyId($token->id)->get();
            return $this->success($subUser);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->error($th->getMessage());
        }
    }
}
