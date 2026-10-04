<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('dashboard', function ($user) {
    // Return true if the user is authorized to see the dashboard (e.g., is an admin)
    return true; // Or $user->isAdmin() if you have roles
});
