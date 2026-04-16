<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('ranin.{orderId}', function ($user, $orderId) {
    // التحقق أن المستخدم لديه هذا الطلب
    return $user->orders()->where('id', $orderId)->exists();
});
 
