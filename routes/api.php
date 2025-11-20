<?php

use App\Http\Controllers\Api\ReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/hello', function (Request $request) {
    return response()->json(['message'=>'Hello API']);
});
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

//Authentikált végpontok
/*
Route::middleware('auth:sanctum')->post('/logout',[AuthController::class,'logout']);

Route::middleware('auth:sanctum')->get('/reservations',[ReservationController::class, 'index']); // összes foglalás
Route::middleware('auth:sanctum')->get('/reservations/{id}',[ReservationController::class, 'show']); // egy foglalás
Route::middleware('auth:sanctum')->post('/reservations',[ReservationController::class, 'store']); // egy foglalás rögzítése
Route::middleware('auth:sanctum')->put('/reservations/{id}',[ReservationController::class, 'update']); // egy foglalás minden mezőjét módosítom
Route::middleware('auth:sanctum')->patch('/reservations/{id}',[ReservationController::class, 'update']); // egy foglalás néhány mezőjét módosítom
Route::middleware('auth:sanctum')->delete('/reservations/{id}',[ReservationController::class, 'destroy']); // egy foglalás törlése
*/

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout',[AuthController::class,'logout']);
    Route::apiResource('reservations', ReservationController::class);
});