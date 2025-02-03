<?php

use App\Models\User;

test('cast', function () {
    $user = new User();
    $reflection = new ReflectionMethod($user, 'casts');
    $reflection->setAccessible(true);

    $casts = $reflection->invoke($user);

    expect($casts)->toBe([
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ]);
});
