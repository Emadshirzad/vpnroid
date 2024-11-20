<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminUsersController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/users",
     *     tags={"AdminUsers"},
     *     summary="list all users",
     *     description="list all Item",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             default="1"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success Message",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="current_page",
     *                 type="integer",
     *                 format="int32",
     *                 description="Current page number"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/UserModel"),
     *                 description="List of item"
     *             ),
     *             @OA\Property(
     *                 property="first_page_url",
     *                 type="string",
     *                 format="uri",
     *                 description="First page URL"
     *             ),
     *             @OA\Property(
     *                 property="from",
     *                 type="integer",
     *                 format="int32",
     *                 description="First item number in the current page"
     *             ),
     *             @OA\Property(
     *                 property="last_page",
     *                 type="integer",
     *                 format="int32",
     *                 description="Last page number"
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="array",
     *                 @OA\Items(
     *                     oneOf={
     *                         @OA\Schema(ref="#/components/schemas/Previous"),
     *                         @OA\Schema(ref="#/components/schemas/Links"),
     *                         @OA\Schema(ref="#/components/schemas/Next")
     *                     }
     *                 ),
     *                 description="Links"
     *             ),
     *             @OA\Property(
     *                 property="last_page_url",
     *                 type="string",
     *                 format="uri",
     *                 description="Last page URL"
     *             ),
     *             @OA\Property(
     *                 property="next_page_url",
     *                 type="string",
     *                 format="uri",
     *                 description="Next page URL"
     *             ),
     *             @OA\Property(
     *                 property="path",
     *                 type="string",
     *                 description="Path"
     *             ),
     *             @OA\Property(
     *                 property="per_page",
     *                 type="integer",
     *                 format="int32",
     *                 description="Items per page"
     *             )
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="an ""unexpected"" error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *     ),security={{"api_key": {}}}
     * )
     * Display the specified resource.
    */
    public function index()
    {
        try {
            return $this->success(User::paginate(10));
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    /**
     * @OA\Put(
     *     path="/admin/users/ban/{id}",
     *     tags={"AdminUsers"},
     *     summary="BanOrUnBanUser",
     *     description="Ban Or UnBan User",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
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
     * Update the specified resource in storage.
     */
    public function ban(int $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->is_ban = !$user->is_ban;
            $user->save();
            $message = $user->is_ban ? 'ban user successfully' : 'unban user successfully';
            return $this->success([
                'message' => $message,
                'user'    => $user
            ]);
        } catch (\Throwable $th) {
            return $this->error('ban error', [$th->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/admin/users/{id}",
     *     tags={"AdminUsers"},
     *     summary="getOneItem",
     *     description="get One Item",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success Message",
     *         @OA\JsonContent(ref="#/components/schemas/UserModel"),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="an ""unexpected"" error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *     ),security={{"api_key": {}}}
     * )
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $user = User::with('userConfig')->find($id);
            return $this->success($user);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->error('Invalid id');
        }
    }

    /**
     * @OA\Put(
     *     path="/admin/users/{id}",
     *     tags={"AdminUsers"},
     *     summary="EditOneItem",
     *     description="edit one Item",
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
     *                 property="username",
     *                 type="string",
     *                 description="username",
     *                 example="Item username"
     *             ),
     *             @OA\Property(
     *                 property="subscription_duration",
     *                 type="integer",
     *                 description="subscription_duration",
     *                 default="null",
     *                 example=1,
     *             ),
     *             @OA\Property(
     *                 property="phone",
     *                 type="string",
     *                 description="phone",
     *                 example=09000000000,
     *             ),
     *             @OA\Property(
     *                 property="address",
     *                 type="string",
     *                 description="address",
     *                 example="Item address",
     *             ),
     *             @OA\Property(
     *                 property="balance",
     *                 type="integer",
     *                 description="user balance",
     *                 example=0,
     *             ),
     *         )
     *     ),
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $dataValidated = $request->validate([
            'username'              => 'nullable|min:6|string',
            'subscription_duration' => 'nullable|numeric',
            'phone'                 => 'nullable|digits:11',
            'address'               => 'nullable|string',
            'balance'               => 'nullable|numeric',
        ]);
        try {
            $user = User::findOrFail($id);
            $user->update($dataValidated);
            return $this->success([
                'edited'=> true,
                $user
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *     path="/admin/users/{id}",
     *     tags={"AdminUsers"},
     *     summary="DeleteOneItem",
     *     description="Delete one Item",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
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
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return $this->success([
                'deleted'=> true,
                $user
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
