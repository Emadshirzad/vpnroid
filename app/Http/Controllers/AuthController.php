<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Envoy;
use App\Models\ListConfig;
use App\Models\subConfig;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['login', 'register']),
            new Middleware('admin', only: ['changeRole']),
        ];
    }

    /**
     * @OA\Post(
     *     path="/auth/user/register",
     *     tags={"Authentication"},
     *     summary="register",
     *     description="register",
     *     operationId="register",
     *     @OA\Response(
     *         response=200,
     *         description="Success Message",
     *         @OA\JsonContent(ref="#/components/schemas/UserModel"),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="an 'unexpected' error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *     ),
     *     @OA\RequestBody(
     *         description="tasks input",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="username",
     *                 type="string",
     *                 description="username ",
     *                 example=""
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
     *     )
     * )
     *
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $dataValidation = $request->validate([
            'username'   => 'required|max:255',
            'password'   => 'required|confirmed|min:6',
        ]);
        $user = User::create($dataValidation);
        return $this->success($user);
    }

    /**
     * @OA\Post(
     *     path="/auth/user/login",
     *     tags={"Authentication"},
     *     summary="login",
     *     description="login",
     *     operationId="login",
     *     @OA\Response(
     *         response=200,
     *         description="Success Message",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessModel"),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="an 'unexpected' error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *     ),
     *     @OA\RequestBody(
     *         description="tasks input",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="username",
     *                 type="string",
     *                 description="username",
     *                 example="Test User"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 description="password",
     *                 example="password",
     *             )
     *         )
     *     )
     * )
     *
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'username'    => 'required',
            'password'    => 'required',
        ]);

        $credentials = request(['username', 'password']);
        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
    }

    /**
     * @OA\Post(
     *     path="/auth/user/role/{id}",
     *     tags={"Authentication"},
     *     summary="changeRole",
     *     description="changeRole (Your must be admin)",
     *     operationId="changeRole",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="tasks input",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="role",
     *                 type="string",
     *                 description="role",
     *                 example="user"
     *             ),
     *             @OA\Property(
     *                 property="phone",
     *                 type="string",
     *                 description="phone (optional)",
     *                 example=""
     *             ),
     *             @OA\Property(
     *                 property="address",
     *                 type="string",
     *                 description="address (optional)",
     *                 example=""
     *             ),
     *             @OA\Property(
     *                 property="balance",
     *                 type="integer",
     *                 description="balance (optional)",
     *                 example=0
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success Message",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessModel"),
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
    public function changeRole(Request $request, int $id)
    {
        try {
            $dataValidation = $request->validate([
                'phone'      => 'nullable|digits:11|numeric',
                'address'    => 'nullable|string',
                'balance'    => 'nullable|numeric',
                'role'       => 'required|string|in:user,envoy,admin',
            ]);
            $user = User::findOrFail($id);
            $user->role = $dataValidation['role'];
            $user->save();
            if ($dataValidation['role'] == 'envoy') {
                $user->update([
                    'phone'      => $dataValidation['phone'],
                    'address'    => $dataValidation['address'],
                    'balance'    => $dataValidation['balance'],
                ]);
            }
            return $this->success($user);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/auth/user/me",
     *     tags={"Authentication"},
     *     summary="my info",
     *     description="my info",
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
    public function me()
    {
        return $this->success(Auth::user());
    }

    /**
     * @OA\Get(
     *     path="/auth/user/config",
     *     tags={"Authentication"},
     *     summary="my config",
     *     description="my info",
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
    public function config()
    {
        try {
            $user = Auth::user()->userConfig;
            if (!$user) {
                return $this->error('config not purchased');
            }
            foreach ($user as $item) {
                $item = $item->config->prepare_id;
            }
            $subs = subConfig::whereSubId($item)->get();
            $subsConfig = [];
            foreach ($subs as $sub) {
                $subsConfig[] = $sub->config;
            }
            return $this->success($subsConfig);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->error('Config not found');
        }
    }

    /**
     * @OA\Post(
     *     path="/auth/user/logout",
     *     tags={"Authentication"},
     *     summary="logout",
     *     description="logout",
     *     operationId="logout",
     *     @OA\Response(
     *         response=200,
     *         description="Success Message",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessModel"),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="an 'unexpected' error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *     ),security={{"api_key": {}}}
     * )
     *
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::logout();
        return $this->success(['message' => 'Successfully logged out']);
    }

    /**
     * @OA\Get(
     *     path="/auth/user/refresh",
     *     tags={"Authentication"},
     *     summary="refresh",
     *     description="refresh a token",
     *     operationId="refresh",
     *     @OA\Response(
     *         response=200,
     *         description="Success Message",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessModel"),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="an 'unexpected' error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *     ),security={{"api_key": {}}}
     * )
     *
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    /**
    * Get the token array structure.
    *
    * @param string $token
    *
    * @return \Illuminate\Http\JsonResponse
    */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => Auth::factory()->getTTL() * 60
        ]);
    }
}
