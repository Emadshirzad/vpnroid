<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    /**
      * @OA\Get(
      *     path="/service",
      *     tags={"Services"},
      *     summary="list all services",
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
            return $this->success(Service::paginate(10));
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/service",
     *     tags={"Services"},
     *     summary="MakeOneItem",
     *     description="make one Item",
     *     @OA\RequestBody(
     *         description="tasks input",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="title",
     *                 example="Item name"
     *             ),
     *             @OA\Property(
     *                 property="price",
     *                 type="integer",
     *                 description="price",
     *                 example=0,
     *             ),
     *             @OA\Property(
     *                 property="type_id",
     *                 type="integer",
     *                 description="type_id",
     *             ),
     *             @OA\Property(
     *                 property="update_time",
     *                 type="integer",
     *                 description="update_time",
     *                 example="10",
     *             )
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
            'name'         => 'required',
            'price'        => 'required|numeric',
            'type_id'      => 'required|numeric',
            'update_time'  => 'required|numeric',
        ]);
        try {
            $service = Service::create($dataValidated);
            return $this->success($service);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/service/{id}",
     *     tags={"Services"},
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
            $service = Service::with('type')->findOrFail($id);
            return $this->success($service);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->error('Invalid id');
        }
    }

    /**
     * @OA\Put(
     *     path="/service/{id}",
     *     tags={"Services"},
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
     *                 property="name",
     *                 type="string",
     *                 description="title",
     *                 example="Item name"
     *             ),
     *             @OA\Property(
     *                 property="price",
     *                 type="integer",
     *                 description="price",
     *                 example=0,
     *             ),
     *             @OA\Property(
     *                 property="type_id",
     *                 type="integer",
     *                 description="type_id",
     *             ),
     *             @OA\Property(
     *                 property="update_time",
     *                 type="integer",
     *                 description="update_time",
     *                 example="10",
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
            'name'         => 'nullable',
            'price'        => 'nullable|numeric',
            'type_id'      => 'nullable|numeric',
            'update_time'  => 'nullable|numeric',
        ]);
        try {
            $service = Service::findOrFail($id);
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
     *     path="/service/{id}",
     *     tags={"Services"},
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
            $service = Service::findOrFail($id);
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
