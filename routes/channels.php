<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('lottery', function ($user) {
    return true; // Allow any authenticated user to listen to lottery jackpot updates
});

Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
