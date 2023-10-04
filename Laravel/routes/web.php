<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::view('/{path?}', 'layouts/app');

require __DIR__ . '/auth.php';

// Get Auth
Route::get('auth', [UserController::class, 'auth']);

// Mailables
// Welcome
Route::get('/mailable/welcome', function () {
    $user = App\Models\User::all()->random();

    $video = App\Models\Video::all()->random();

    return new App\Mail\WelcomeMail($user->username, $video);
});

// Audio Receipt
Route::get('/mailable/audio-receipt', function () {
    $audios = App\Models\Audio::all();

    return new App\Mail\AudioReceiptMail($audios);
});

// Video Receipt
Route::get('/mailable/video-receipt', function () {
    $videos = App\Models\Video::all();

    return new App\Mail\VideoReceiptMail($videos);
});

// Deco
Route::get('/mailable/deco', function () {
    $user = App\Models\User::all()->random();
    $user2 = App\Models\User::all()->random();

    return new App\Mail\DecoMail($user->username, $user2->username);
});