<?php

namespace App\Http\Controllers;

use App\Models\Channels;
use App\Models\Channelss;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;

class ChannelsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('admin', only: ['update', 'destroy']),
        ];
    }

    /**
     * @OA\Get(
     *     path="/channel",
     *     tags={"Channels"},
     *     summary="list all channel",
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
            return $this->success(Channels::paginate(10));
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/channel",
     *     tags={"Channels"},
     *     summary="MakeOneItem",
     *     description="make one Item",
     *     @OA\RequestBody(
     *         description="tasks input",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="link",
     *                 type="string",
     *                 description="title",
     *                 example="Item link"
     *             ),
     *             @OA\Property(
     *                 property="is_encode",
     *                 type="string",
     *                 description="is_encode",
     *                 example=false,
     *             ),
     *             @OA\Property(
     *                 property="service_id",
     *                 type="integer",
     *                 description="service_id",
     *                 example=1,
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
            'link'        => 'required',
            'is_encode'   => 'required',
            'service_id'  => 'required',
        ]);
        try {
            $link = Channels::create($dataValidated);
            return $this->success($link);
        } catch (Exception $th) {
            return $this->error($th->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/channel/{id}",
     *     tags={"Channels"},
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
            $channel = Channels::with('service')->find($id);
            return $this->success($channel);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->error('Invalid id');
        }
    }

    /**
     * @OA\Put(
     *     path="/channel/{id}",
     *     tags={"Channels"},
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
     *                 property="link",
     *                 type="string",
     *                 description="link",
     *                 example="Item name"
     *             ),
     *             @OA\Property(
     *                 property="is_encode",
     *                 type="boolean",
     *                 description="is_encode",
     *                 default="null",
     *                 example=true,
     *             ),
     *             @OA\Property(
     *                 property="service_id",
     *                 type="integer",
     *                 description="service_id",
     *                 example=1,
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
            'link'        => 'nullable',
            'is_encode'   => 'boolean',
            'service_id'  => 'numeric',
        ]);
        try {
            $channel = Channels::findOrFail($id);
            $channel->update($dataValidated);
            return $this->success([
                'edited'=> true,
                $channel
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *     path="/channel/{id}",
     *     tags={"Channels"},
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
            $channel = Channels::findOrFail($id);
            $channel->delete();
            return $this->success([
                'deleted'=> true,
                $channel
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
