<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsenController;

use App\Http\Controllers\Api\RfidController;

Route::post('/absen', [AbsenController::class, 'store']);
Route::post('/rfid', [RfidController::class, 'store']);
