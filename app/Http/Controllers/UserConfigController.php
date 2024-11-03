<?php

namespace App\Http\Controllers;

use App\Models\Channels;
use App\Models\LinkSub;
use App\Models\Type;
use App\Models\User;
use App\Models\userConfig;
use App\Models\WebServiceGet;
use App\Models\WebServicePost;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserConfigController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user-config",
     *     tags={"userConfigs"},
     *     summary="list all types",
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
     *                 @OA\Items(ref="#/components/schemas/ConfigModel"),
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
            return $this->success(userConfig::paginate(10));
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/user-config",
     *     tags={"userConfigs"},
     *     summary="MakeOneItem",
     *     description="make one Item",
     *     @OA\RequestBody(
     *         description="tasks input",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="user_id",
     *                 type="integer",
     *                 description="user id",
     *             ),
     *             @OA\Property(
     *                 property="config_id",
     *                 type="integer",
     *                 description="config id",
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dataValidated = $request->validate([
            'user_id'   => 'required|numeric',
            'config_id' => 'required|numeric'
        ]);
        try {
            $UserConfig = userConfig::create($dataValidated);
            $user = $UserConfig->user;
            $subscription = $UserConfig->config;
            $type = Type::findOrFail($subscription->prepare);
            switch ($type->name) {
                case 'Channel':
                    $resp = Channels::FindOrFail($subscription->prepare_id);
                    break;
                case 'Link':
                    $resp = LinkSub::FindOrFail($subscription->prepare_id);
                    break;
                case 'GET':
                    $resp = WebServiceGet::FindOrFail($subscription->prepare_id);
                    break;
                case 'POST':
                    $resp = WebServicePost::FindOrFail($subscription->prepare_id);
                    break;
            }
            $service = $resp->service->update_time;
            // return $this->success([
            //     $subscription,
            //     $user,
            //     $type,
            //     $resp,
            //     $service
            // ]);
            $user->update([
                'last_purchase_date'   => now(),
                'subscription_duration'=> $service
            ]);
            /** @var User */
            $envoy = Auth::user();
            $amount = $envoy->balance - $resp->service->price;
            $envoy->update(['balance' => $amount]);
            return $this->success($UserConfig);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return $this->error('Something went wrong');
        }
    }

    /**
     * @OA\Get(
     *     path="/user-config/{id}",
     *     tags={"userConfigs"},
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
     *         @OA\JsonContent(ref="#/components/schemas/SuccessModel"),
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
            $service = userConfig::findOrFail($id);
            return $this->success($service);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->error('Invalid id');
        }
    }

    /**
     * @OA\Put(
     *     path="/user-config/{id}",
     *     tags={"userConfigs"},
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
     *                 property="user_id",
     *                 type="Integer",
     *                 description="user id",
     *             ),
     *             @OA\Property(
     *                 property="config_id",
     *                 type="Integer",
     *                 description="config id",
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $dataValidated = $request->validate([
            'user_id'   => 'nullable|numeric',
            'config_id' => 'nullable|numeric'
        ]);
        try {
            $UserConfig = userConfig::findOrFail($id);
            $user = $UserConfig->user;
            $subscription = $UserConfig->config;
            $type = Type::findOrFail($subscription->prepare);
            switch ($type->name) {
                case 'Channel':
                    $resp = Channels::FindOrFail($subscription->prepare_id);
                    break;
                case 'Link':
                    $resp = LinkSub::FindOrFail($subscription->prepare_id);
                    break;
                case 'GET':
                    $resp = WebServiceGet::FindOrFail($subscription->prepare_id);
                    break;
                case 'POST':
                    $resp = WebServicePost::FindOrFail($subscription->prepare_id);
                    break;
            }
            $service = $resp->service->update_time;
            // return $this->success([
            //     $subscription,
            //     $user,
            //     $type,
            //     $resp,
            //     $service
            // ]);
            $user->update([
                'last_purchase_date'   => now(),
                'subscription_duration'=> $service
            ]);
            /** @var User */
            $envoy = Auth::user();
            $amount = $envoy->balance - $resp->service->price;
            $envoy->update(['balance' => $amount]);
            $service->update($dataValidated);
            return $this->success([
                'edited'=> true,
                $service
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *     path="/user-config/{id}",
     *     tags={"userConfigs"},
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
            $service = userConfig::findOrFail($id);
            $service->delete();
            return $this->success([
                'deleted'=> true,
                $service
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
