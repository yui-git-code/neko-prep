<?php

use App\Livewire\Diagnosis\History;
use App\Livewire\Diagnosis\Quiz;
use App\Livewire\Diagnosis\Result;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');

    Route::get('/quiz', Quiz::class)->name('quiz');
    Route::get('/result/{id}', Result::class)->name('result');


Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function(){
        return view('dashboard');
    })->name('dashboard');

    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');


    Route::get('/history', History::class)->name('history');


});


