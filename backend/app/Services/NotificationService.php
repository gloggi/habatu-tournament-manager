<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Option;
use App\Notifications\PushNotification;
use Carbon\Carbon;

class NotificationService
{
    public function notify()
    {
        $options = Option::first();
        $timeRangeStart = Carbon::now();
        $timeRangeEnd = $timeRangeStart->copy()->addMinutes($options->break_duration);
        $gamesInRange = Game::where('temporary', false)->whereHas('timeslot', function ($query) use ($timeRangeStart, $timeRangeEnd) {
            $query->where('start_time', '>=', $timeRangeStart->toTimeString())
                ->where('start_time', '<=', $timeRangeEnd->toTimeString());
        })->with('teamA.members', 'teamB.members', 'hall', 'timeslot')->get();

        $this->notifyTeamMembers($gamesInRange);

    }

    private function notifyTeamMembers($games)
    {
        foreach ($games as $game) {
            if ($game->notifiedUsers()->count() == 0) {
                $this->sendTeamNotification($game->teamA, $game->teamB, $game);
                $this->sendTeamNotification($game->teamB, $game->teamA, $game);
            }
        }

    }

    private function sendTeamNotification($userTeam, $opponentTeam, $game)
    {
        [$title, $body] = $this->generateTeamNotificationMessage($game, $opponentTeam);
        foreach ($userTeam->members as $member) {
            $member->notify(new PushNotification($title, $body));
            $game->notifiedUsers()->attach($member->id);
        }

    }

    private function generateTeamNotificationMessage($game, $opponentTeam)
    {
        $minuteDifference = round(Carbon::now()->diffInMinutes($game->timeslot->start_time), 0);
        $minuteDifferenceString = $minuteDifference == 1 ? 'einer Minute' : "$minuteDifference Minuten";

        $startTime = $game->timeslot->start_time->toTimeString('minute');
        $hallName = $game->hall->name;
        $opponentName = $opponentTeam->name;

        // Titel-Varianten (Startzeit und Halle sind immer enthalten)
        $titles = [
            "🏀 Nächstes Spiel um {$startTime} in {$hallName}!",
            "⏰ Spielzeit! Um {$startTime} in {$hallName}",
            "🏟️ Nächstes Spiel um {$startTime} in {$hallName}",
            "🕒 Bald geht's los! Um {$startTime} in {$hallName}",
            "🏀 Bereit für das Spiel um {$startTime} in {$hallName}?",
        ];

        // Body-Varianten (zufällig und mit Emojis)
        $bodies = [
            "Das Spiel gegen {$opponentName} beginnt in {$minuteDifferenceString}. 🏃‍♂️ Begib dich mit deinem Team in die Halle!",
            "Gegen {$opponentName} geht es in {$minuteDifferenceString} los. 🏀 Schnapp dir dein Team und ab in die Halle!",
            "Noch {$minuteDifferenceString} bis zum Anpfiff gegen {$opponentName}. 🏃‍♀️ Los, mach dich bereit!",
            "Das Spiel gegen {$opponentName} startet in {$minuteDifferenceString}. 🏃‍♀️ Auf in die Halle, das Team wartet!",
            "Gegen {$opponentName} geht es in {$minuteDifferenceString} los. 🏃‍♂️ Schnell, ab in die Halle!",
        ];

        // Wähle zufällige Titel- und Body-Varianten aus
        $title = $titles[array_rand($titles)];
        $body = $bodies[array_rand($bodies)];

        return [$title, $body];
    }
}
