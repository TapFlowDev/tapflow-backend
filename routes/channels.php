<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Group_member;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
// Broadcast::channel('notifications', function ($user) {
//     return true;
// });
Broadcast::channel('private-notifications', function () {
   return "hiiiiiiiiiiiiii";
   
});
// Broadcast::channel('user.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });