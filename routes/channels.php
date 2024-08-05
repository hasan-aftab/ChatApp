<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

Broadcast::channel('send-message.{receiverId}', function ($user, $receiverId) {
    return (int)$user->id === (int)$receiverId;
});

Broadcast::channel('chat.{userId}', function ($user, $userId) {
    return (int)$user->id === (int)$userId;
});
