<?php

namespace App\Http\Controllers;

use App\Models\Channels;
use App\Models\Config;
use App\Models\Service;
use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\Settings\Connection;
use danog\MadelineProto\Stream\Proxy\SocksProxy;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConfigController extends Controller
{
    /**
     * @OA\Get(
     *     path="/config",
     *     tags={"Configs"},
     *     summary="listAllItem",
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
            return $this->success(Config::paginate(20));
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/config/tel",
     *     tags={"Configs"},
     *     summary="getConfigFromTel (get from channels)",
     *     description="get Config From Tel (get from channels)",
     *     @OA\Response(
     *         response=200,
     *         description="Success Message",
     *         @OA\JsonContent(ref="#/components/schemas/ConfigModel"),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="an ""unexpected"" error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *     ),security={{"api_key": {}}}
     * )
     * Display the specified resource.
     */
    public function getConfigFromTel()
    {
        try {
            $settings = new Settings();
            $proxy = new Connection();
            $proxy->addProxy(
                SocksProxy::class,
                [
                    'address' => '127.0.0.1',
                    'port'    => 8086,
                ]
            );
            $settings->setConnection($proxy);
            $app = (new AppInfo())
                ->setApiId(27454881)
                ->setApiHash('11c79e528a856d2c8408a6e3f14ea397');
            $settings->setAppInfo($app);
            $MadelineProto = new API(Storage::path('session.madeline'), $settings);

            $MadelineProto->start();
            $channelsId = Channels::all();
            foreach ($channelsId as $channel) {
                try {
                    $MadelineProto->channels->joinChannel(['channel' => $channel->link]);
                    $messages = $MadelineProto->messages->getHistory([
                        'peer'  => $channel->link,
                        'limit' => 5,
                    ])['messages'];
                } catch (\Throwable $th) {
                    Log::error($th->getMessage());
                }
            }
            foreach ($messages as $message) {
                $pattern = '/(v[l|m]ess|tcp|h2|ss|trojan):\/\/[^\s]+/';
                preg_match_all($pattern, $message['message'], $matches);
                $urls = $matches[0];
                foreach ($urls as $url) {
                    try {
                        if (!preg_match($pattern, $url, $matches2)) {
                            echo 'not found type' . "\n";
                        }
                        $type = $matches2[1];
                        // echo $type . "\n";
                        Config::create([
                            'url'         => $url,
                            'type'        => $type,
                            'channels_id' => $channel->id
                        ]);
                    } catch (\Exception $e) {
                        if ($e->getCode() == 23000) {
                            echo 'duplicate entry for url and type, skipping insert' . "\n";
                        } else {
                            echo $e->getMessage() . "\n";
                        }
                    }
                }
            }
            return 'config saved successfully';
        } catch (Exception $th) {
            return $th->getMessage();
        }
    }

    /**
     * @OA\Get(
     *     path="/config/setChannel",
     *     tags={"Configs"},
     *     summary="setChannel",
     *     description="setChannel",
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
    public function setChannels()
    {
        try {
            $settings = new Settings();
            $proxy = new Connection();
            $proxy->addProxy(
                SocksProxy::class,
                [
                    'address' => '127.0.0.1',
                    'port'    => 8086,
                ]
            );
            $settings->setConnection($proxy);
            $app = (new AppInfo())
                ->setApiId(27454881)
                ->setApiHash('11c79e528a856d2c8408a6e3f14ea397');
            $settings->setAppInfo($app);
            $MadelineProto = new API(Storage::path('session.madeline'), $settings);

            $MadelineProto->start();
            $channels = $MadelineProto->getDialogIds();
            $channels = array_filter($channels, function ($channel) {
                if (Str::startsWith($channel, '-')) {
                    return $channel;
                }
            });
            $service = Service::whereTypeId(2)->first(); // id 2 == channel
            foreach ($channels as $channel) {
                try {
                    Channels::create([
                        'link'       => $channel,
                        'service_id' => $service->id,
                    ]);
                } catch (\Throwable $th) {
                    Log::error($th->getMessage());
                }
            }
            return ['message' => 'joined and saved channels successfully'];
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
