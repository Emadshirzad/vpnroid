<?php

use App\Http\Controllers\ConfigController;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// write schedule job get configs
Schedule::call(function () {
    return (new ConfigController())->getConfigFromTel(request());
})->hourly();
// write schedule job set channels
Schedule::call(function () {
    return (new ConfigController())->setChannels(request());
})->hourly();
