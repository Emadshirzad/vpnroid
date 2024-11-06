<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChannelsController;
use App\Http\Controllers\CheckListController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\EnvoyController;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\LinkSubController;
use App\Http\Controllers\ListConfigController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TypeConfigController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserConfigController;
use App\Http\Controllers\WebServiceGetController;
use App\Http\Controllers\WebServicePostController;
use Illuminate\Support\Facades\Route;

Route::get('/test', [ExampleController::class, 'test']);

Route::group([
    'prefix' => 'auth'
], function () {
    Route::group(['prefix' => 'user'], function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('register', [AuthController::class, 'register']);
        Route::get('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('role/{id}', [AuthController::class, 'changeRole']);
    });

    Route::group(['prefix' => 'envoy'], function () {
        Route::post('register-user', [EnvoyController::class, 'registerUser']);
        Route::get('envoy-users', [EnvoyController::class, 'show']);
    });
});

Route::group([
    'prefix'     => 'config',
    'middleware' => ['envoy']
], function () {
    Route::get('/', [ConfigController::class, 'index']);
    Route::get('/tel', [ConfigController::class, 'getConfigFromTel']);
    Route::get('/setChannel', [ConfigController::class, 'setChannels']);
});

Route::group([
    'middleware' => ['envoy']
], function () {
    Route::apiResource('sub', LinkSubController::class, [
        'parameters'=> [
            'sub'=> 'id'
        ]
    ]);
});

Route::group([
    'middleware' => ['envoy']
], function () {
    Route::apiResource('service', ServiceController::class, [
        'parameters'=> [
            'service'=> 'id'
        ]
    ]);
});

Route::group([
    'middleware' => ['envoy']
], function () {
    Route::apiResource('type', TypeController::class, [
        'parameters'=> [
            'type'=> 'id'
        ]
    ]);
});

Route::group([
    'middleware' => ['envoy']
], function () {
    Route::apiResource('type-config', TypeConfigController::class, [
        'parameters'=> [
            'type-config'=> 'id'
        ]
    ]);
});

Route::group([
    'middleware' => ['envoy']
], function () {
    Route::apiResource('channel', ChannelsController::class, [
        'parameters'=> [
            'channel'=> 'id'
        ]
    ]);
});

Route::group([
    'prefix'     => 'webService',
    'middleware' => ['envoy']
], function () {
    Route::group([], function () {
        Route::apiResource('get', WebServiceGetController::class, [
            'parameters'=> [
                'get'=> 'id'
            ]
        ]);
    });

    Route::group([], function () {
        Route::apiResource('post', WebServicePostController::class, [
            'parameters'=> [
                'post'=> 'id'
            ]
        ]);
    });
});

Route::group([
    'middleware' => ['envoy']
], function () {
    Route::apiResource('operator', OperatorController::class, [
        'parameters'=> [
            'operator'=> 'id'
        ]
    ]);
});

Route::group([
    'middleware' => ['envoy']
], function () {
    Route::apiResource('user-config', UserConfigController::class, [
        'parameters'=> [
            'user-config'=> 'id'
        ]
    ]);
});

Route::group([
    'middleware' => ['envoy']
], function () {
    Route::apiResource('list-config', ListConfigController::class, [
        'parameters'=> [
            'list-config'=> 'id'
        ]
    ]);
});

Route::group([
    'middleware' => ['admin']
], function () {
    Route::apiResource('check', CheckListController::class, [
        'parameters'=> [
            'check'=> 'id'
        ]
    ]);
});
