<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use App\Http\Controllers\VideoController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/generate', [VideoController::class, 'generate'])->middleware('auth');

Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('login.google');

Route::get('/auth/google/callback', function () {
    $user = Socialite::driver('google')->user();

    $existingUser = User::where('email', $user->email)->first();

    if ($existingUser) {
        Auth::login($existingUser);
    } else {
        $newUser = User::create([
            'name' => $user->name,
            'email' => $user->email,
            'password' => bcrypt(Str::random(16)),
        ]);
        Auth::login($newUser);
    }

    return redirect(route('home', absolute: false))->with('success', 'Login successful!');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
