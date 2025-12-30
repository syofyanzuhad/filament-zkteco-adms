<?php

use Illuminate\Support\Facades\Route;
use Syofyanzuhad\FilamentZktecoAdms\Http\Controllers\CDataController;
use Syofyanzuhad\FilamentZktecoAdms\Http\Controllers\DeviceCmdController;
use Syofyanzuhad\FilamentZktecoAdms\Http\Controllers\GetRequestController;

// /iclock/cdata - Main data endpoint (handles both GET and POST)
Route::match(['get', 'post'], 'cdata', CDataController::class)->name('cdata');

// /iclock/getrequest - Device polls for pending commands
Route::get('getrequest', GetRequestController::class)->name('getrequest');

// /iclock/devicecmd - Device command acknowledgment
Route::match(['get', 'post'], 'devicecmd', DeviceCmdController::class)->name('devicecmd');

// /iclock/test - Connection test endpoint
Route::match(['get', 'post'], 'test', fn () => response('OK'))->name('test');
