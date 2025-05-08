<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('make:main-user', function () {
    $user = User::firstOrCreate([
        'name' => 'Main User',
        'email' => 'example@example.com'
    ], ['password' => Hash::make('password')]);

    $token = $user->createToken('app-token');

    echo 'token: ' . $token->plainTextToken . "\n";
})->purpose('Make a main user account, and get auth token');
