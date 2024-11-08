<?php

namespace App\Http\Controllers;

use App\Models\WebServicePost;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;

class WebServicePostController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('admin', only: ['update', 'destroy']),
        ];
    }

    /**
     * @OA\Get(
     *     path="/webService/post",
     *     tags={"WebServicesPost"},
     *     summary="list all get",
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
            return $this->success(WebServicePost::paginate(10));
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/webService/post",
     *     tags={"WebServicesPost"},
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
     *                 property="body",
     *                 type="string",
     *                 description="body",
     *                 example="Item body"
     *             ),
     *             @OA\Property(
     *                 property="key",
     *                 type="string",
     *                 description="key",
     *                 example="Item key"
     *             ),
     *             @OA\Property(
     *                 property="header",
     *                 type="string",
     *                 description="header",
     *                 example="Item header"
     *             ),
     *             @OA\Property(
     *                 property="is_encode",
     *                 type="boolean",
     *                 description="is_encode",
     *                 example=false,
     *             ),
     *             @OA\Property(
     *                 property="service_id",
     *                 type="integer",
     *                 description="service_id",
     *                 example=0,
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
            'link'      => 'required',
            'body'      => 'required',
            'key'       => 'required',
            'header'    => 'required',
            'is_encode' => 'required|boolean',
            'service_id'=> 'required|integer',
        ]);
        try {
            $link = WebServicePost::create($dataValidated);
            return $this->success($link);
        } catch (Exception $th) {
            return $this->error($th->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/webService/post/{id}",
     *     tags={"WebServicesPost"},
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
            $webPost = WebServicePost::with('service')->find($id);
            return $this->success($webPost);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->error('Invalid id');
        }
    }

    /**
     * @OA\Put(
     *     path="/webService/post/{id}",
     *     tags={"WebServicesPost"},
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
     *                 description="title",
     *                 example="Item link"
     *             ),
     *             @OA\Property(
     *                 property="body",
     *                 type="string",
     *                 description="body",
     *                 example="Item body"
     *             ),
     *             @OA\Property(
     *                 property="key",
     *                 type="string",
     *                 description="key",
     *                 example="Item key"
     *             ),
     *             @OA\Property(
     *                 property="header",
     *                 type="string",
     *                 description="header",
     *                 example="Item header"
     *             ),
     *             @OA\Property(
     *                 property="is_encode",
     *                 type="boolean",
     *                 description="is_encode",
     *                 example=false,
     *             ),
     *             @OA\Property(
     *                 property="service_id",
     *                 type="integer",
     *                 description="service_id",
     *                 example=0,
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $dataValidated = $request->validate([
            'link'      => 'required',
            'body'      => 'required',
            'key'       => 'required',
            'header'    => 'required',
            'is_encode' => 'required|boolean',
        ]);
        try {
            $webPost = WebServicePost::findOrFail($id);
            $webPost->update($dataValidated);
            return $this->success([
                'edited'=> true,
                $webPost
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *     path="/webService/post/{id}",
     *     tags={"WebServicesPost"},
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
            $webPost = WebServicePost::findOrFail($id);
            $webPost->delete();
            return $this->success([
                'deleted'=> true,
                $webPost
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
