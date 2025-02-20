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
        $minuteDifferenceString = $minuteDifference == 1 ? 'einere Minutä' : "$minuteDifference Minutä";

        $startTime = $game->timeslot->start_time->toTimeString('minute');
        $hallName = $game->hall->name;
        $opponentName = $opponentTeam->name;

        $titles = [
            "🏀 S nächschte Spiel am {$startTime} ih de {$hallName}!",
            "⏰ Spiilziit! Am {$startTime} ih de {$hallName}",
            "🏟️ S nächschte Spiel am {$startTime} ih de {$hallName}",
            "🕒 Bald gaht's los! Am {$startTime} ih de {$hallName}",
            "🏀 Parat fürs Spiel am {$startTime} ih de {$hallName}?",
        ];

        $bodies = [
            "S Spiel gege {$opponentName} fangt ih {$minuteDifferenceString} ah. 🏃‍♂️ Begib dich mit dim Team id Halle!",
            "Gäge {$opponentName} gahts ih {$minuteDifferenceString} los. 🏀 Schnapp dir dis Team und ab ih d Halle!",
            "No {$minuteDifferenceString} bis zum Anpfiff gäge {$opponentName}. 🏃‍♀️ Los, mached eu parat!",
            "S Spiel gege {$opponentName} startet ih {$minuteDifferenceString}. 🏃‍♀️ Ab id Halle!",
            "Gäge {$opponentName} gahts ih {$minuteDifferenceString} los. 🏃‍♂️ Schnell, ab id Halle!",
        ];

        $title = $titles[array_rand($titles)];
        $body = $bodies[array_rand($bodies)];

        return [$title, $body];
    }
}
