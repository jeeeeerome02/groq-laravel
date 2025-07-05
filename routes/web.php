<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

// In routes/web.php
Route::get('/', [ChatController::class, 'showChat']);
Route::post('/chat/send', [ChatController::class, 'sendMessage']);

