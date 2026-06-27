<?php

use App\Models\User;

test('filament avatar url uses the stored public avatar path', function () {
    $user = new User([
        'avatar' => 'avatars/budi.png',
    ]);

    expect($user->getFilamentAvatarUrl())->toBe(url('/storage/avatars/budi.png'));
});
