<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Team;
use App\Models\User;
use App\Notifications\PushNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

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

        $user = $request->user('api');

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

    public function notifyTeamMembers()
    {
        $notificationService = new NotificationService;
        $notificationService->notify();

        return response()->json(['success' => true], 200);

    }

    public function remindTeamMembers(Request $request)
    {

        $validated = $request->validate([
            'team_id' => 'required|integer',
            'game_id' => 'required|integer',
        ]);

        $game = Game::find($validated['game_id'])->with(['hall', 'timeslot'])->first();
        $teamMembers = Team::find($validated['team_id'])->members()->get();

        foreach ($teamMembers as $member) {
            $member->notify(new PushNotification('🚨 Spielerinnerig',
                "Begib dich mit dim Team id {$game->hall->name} fürs Spiel am {$game->timeslot->start_time->toTimeString('minute')}"));
        }

        return response()->json(['success' => true], 200);

    }

    public function remindReferees(Request $request)
    {

        $validated = $request->validate([
            'game_id' => 'required|integer',
        ]);

        $game = Game::with(['hall', 'timeslot', 'referees'])->findOr($validated['game_id']);
        $referees = $game->referees;

        foreach ($referees as $referee) {
            $referee->notify(new PushNotification('🚨 Schiriiinnerig',
                "Begib dich id {$game->hall->name} spiel am {$game->timeslot->start_time->toTimeString('minute')} z pfiife"));
        }

        return response()->json(['success' => true], 200);

    }
}
