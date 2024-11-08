<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;

class OperatorController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('admin', only: ['update', 'destroy']),
        ];
    }

    /**
    * @OA\Get(
    *     path="/operator",
    *     tags={"Operators"},
    *     summary="list all operators",
    *     description="list all Operator",
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
            return $this->success(Operator::paginate(10));
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/operator",
     *     tags={"Operators"},
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
     *                 property="limit_number",
     *                 type="integer",
     *                 description="limit_number",
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
            'name'         => 'required',
            'limit_number' => 'required',
        ]);
        try {
            $service = Operator::create($dataValidated);
            return $this->success($service);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/operator/{id}",
     *     tags={"Operators"},
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
            $service = Operator::findOrFail($id);
            return $this->success($service);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->error('Invalid id');
        }
    }

    /**
     * @OA\Put(
     *     path="/operator/{id}",
     *     tags={"Operators"},
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
     *                 property="limit_number",
     *                 type="integer",
     *                 description="title",
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $dataValidated = $request->validate([
            'name'         => 'nullable',
            'limit_number' => 'nullable',
        ]);
        try {
            $service = Operator::findOrFail($id);
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
     *     path="/operator/{id}",
     *     tags={"Operators"},
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
            $service = Operator::findOrFail($id);
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
