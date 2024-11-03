<?php

namespace App\Http\Controllers;

use App\Models\Channels;
use App\Models\LinkSub;
use App\Models\ListConfig;
use App\Models\Type;
use App\Models\WebServiceGet;
use App\Models\WebServicePost;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ListConfigController extends Controller
{
    /**
     * @OA\Get(
     *     path="/list-config",
     *     tags={"ListConfigs"},
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
            return $this->success(ListConfig::paginate(10));
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/list-config",
     *     tags={"ListConfigs"},
     *     summary="MakeOneItem",
     *     description="make one Item",
     *     @OA\RequestBody(
     *         description="tasks input",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="type_id",
     *                 type="integer",
     *                 description="type config",
     *             ),
     *             @OA\Property(
     *                 property="prepare",
     *                 type="integer",
     *                 description="prepare config",
     *             ),
     *             @OA\Property(
     *                 property="prepare_id",
     *                 type="integer",
     *                 description="prepare id config",
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
            'type_id'   => 'required|numeric',
            'prepare'   => 'required|numeric',
            'prepare_id'=> 'required|numeric',
        ]);
        try {
            $config = ListConfig::create($dataValidated);
            $type = Type::findOrFail($config->prepare);
            switch ($type->name) {
                case 'Channel':
                    $resp = Channels::FindOrFail($config->prepare_id);
                    $urls = [];
                    foreach ($resp->config as $url) {
                        $urls[] = $url->url;
                    }
                    $resp = $urls;
                    break;
                case 'Link':
                    $resp = LinkSub::FindOrFail($config->prepare_id);
                    break;
                case 'GET':
                    $resp = WebServiceGet::FindOrFail($config->prepare_id);
                    break;
                case 'POST':
                    $resp = WebServicePost::FindOrFail($config->prepare_id);
                    break;
            }
            $config->update(['config' => $resp->link ?? $resp]);
            return $this->success($config);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/list-config/{id}",
     *     tags={"ListConfigs"},
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
            $service = ListConfig::findOrFail($id);
            return $this->success($service);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *     path="/list-config/{id}",
     *     tags={"ListConfigs"},
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
     *                 property="type_id",
     *                 type="integer",
     *                 description="type config",
     *             ),
     *             @OA\Property(
     *                 property="prepare",
     *                 type="integer",
     *                 description="prepare config",
     *             ),
     *             @OA\Property(
     *                 property="prepare_id",
     *                 type="integer",
     *                 description="prepare id config",
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
            'type_id'   => 'nullable|numeric',
            'prepare'   => 'nullable|numeric',
            'prepare_id'=> 'nullable|numeric',
        ]);
        try {
            $config = ListConfig::findOrFail($id);
            $type = Type::findOrFail($config->prepare);
            switch ($type->name) {
                case 'Channel':
                    $resp = Channels::FindOrFail($config->prepare_id);
                    $urls = [];
                    foreach ($resp->config as $url) {
                        $urls[] = $url->url;
                    }
                    $resp = $urls;
                    break;
                case 'Link':
                    $resp = LinkSub::FindOrFail($config->prepare_id);
                    break;
                case 'GET':
                    $resp = WebServiceGet::FindOrFail($config->prepare_id);
                    break;
                case 'POST':
                    $resp = WebServicePost::FindOrFail($config->prepare_id);
                    break;
            }
            $config->update([
                'type_id'    => $dataValidated['type_id'],
                'prepare'    => $dataValidated['prepare'],
                'prepare_id' => $dataValidated['prepare_id'],
                'config'     => $resp->link ?? $resp
            ]);
            return $this->success([
                'edited'=> true,
                $config
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *     path="/list-config/{id}",
     *     tags={"ListConfigs"},
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
            $config = ListConfig::findOrFail($id);
            $config->delete();
            return $this->success([
                'deleted'=> true,
                $config
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
