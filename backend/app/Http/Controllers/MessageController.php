<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\PushNotification;

class MessageController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|url',
            'expirationTime' => 'nullable',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $user = $request->user("api");

        $user->updatePushSubscription(
            $validated['endpoint'],
            $validated['keys']['p256dh'],
            $validated['keys']['auth']
        );

        return response()->json(['success' => true], 200);
    }

    public function sendMessageToAllUsers(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
        ]);
        error_log("Hello");

        $users = User::whereNotNull('push_subscriptions')->get();
        error_log($users->count());

        foreach ($users as $user) {
            $user->notify(new PushNotification($validated['title'], $validated['body']));
        }

        return response()->json(['success' => true], 200);
    }

    public function sendMessageToUser(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'user_id' => 'required|integer',
        ]);

        $user = User::find($validated['user_id']);
        $user->notify(new PushNotification($validated['title'], $validated['body']));

        return response()->json(['success' => true], 200);
    }
}