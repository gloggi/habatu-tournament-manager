<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Game;
use App\Models\Group;
use App\Models\Hall;
use App\Models\Option;
use App\Models\Team;
use App\Models\Timeslot;

class TournamentService
{
    private $temporary;

    private $options;

    private $finalsDict = [
        1 => 'Finale',
        2 => 'Halbfinale',
        4 => 'Viertelfinale',
        8 => 'Achtelfinale',
        16 => 'Achtundzwanzigstelfinale',
        32 => 'Zweiunddreißigstelfinale',
        64 => 'Vierundsechzigstelfinale',
        128 => 'Hundertachtundzwanzigstelfinale',
    ];

    public function __construct($temporary = false)
    {
        $this->temporary = $temporary;
        $this->options = Option::first();

    }

    public function createTournament($temporary = false)
    {
        $this->clearTournament();
        $categories = Category::all();
        $gamesByCategory = [];
        foreach ($categories as $category) {
            $gamesByCategory[$category->id] = $this->organizeCategoryMatches($category);
            foreach ($gamesByCategory[$category->id] as $game) {
            }
        }

        $mergedGames = $this->zipMergeGames($gamesByCategory);
        $this->createTimeTable($mergedGames);
        $this->findTournamentConflicts();
        $this->createFinals();

        return $this->getTournamentInfos();

    }

    private function clearTournament()
    {
        // Delete all temporary entries
        Game::where('temporary', true)->delete();
        Timeslot::where('temporary', true)->delete();
        Group::where('temporary', true)->delete();
        Team::where('temporary', true)->delete();

        if (! $this->temporary) {
            // Use delete() instead of truncate() to respect foreign key constraints
            Game::query()->delete();
            Timeslot::query()->delete();
            Group::query()->delete();

            // Delete only dummy teams explicitly
            Team::where('dummy', true)->delete();
        }
    }

    private function getTournamentInfos()
    {
        $games = Game::all();
        $lastGameTime = Game::orderBy('timeslot_id', 'desc')->first()->timeslot->end_time;

        return [
            'game_count' => $games->count(),
            'last_game_time' => $lastGameTime->format('H:i'),
        ];
    }

    private function createFinals()
    {
        $allCategories = Category::all();
        $finalTypesPerCategorie = [];
        $subgroupsPerCategorie = [];
        foreach ($allCategories as $category) {
            $finalTypesPerCategorie[$category->id] = $category->subgroups;
            $subgroupsPerCategorie[$category->id] = $category->subgroups;
        }

        $maxFinaleType = Category::max('subgroups');
        while ($maxFinaleType >= 1) {
            [$timeslot, $hall] = $this->getTimeAndHallSlot(newSlot: true);
            if ($maxFinaleType == 1) {
                $this->createPlayForThirdGames();
            }
            foreach ($finalTypesPerCategorie as $category => $finalType) {
                if ($finalType != $maxFinaleType) {
                    continue;
                }
                for ($i = 0; $i < $finalType; $i++) {
                    [$timeslot, $hall] = $this->getTimeAndHallSlot(newSlot: false);
                    [$dummyTeamOne, $dummyTeamTwo] = $this->createDummyTeams($category, $subgroupsPerCategorie[$category] == $finalType, $i, $finalType);
                    Game::create([
                        'team_a_id' => $dummyTeamOne->id,
                        'team_b_id' => $dummyTeamTwo->id,
                        'category_id' => $category,
                        'finale_type' => $finalType,
                        'temporary' => $this->temporary,
                        'timeslot_id' => $timeslot->id,
                        'hall_id' => $hall->id,
                    ]);
                }
                if ($maxFinaleType == 1) {

                    $this->getTimeAndHallSlot(newSlot: true);
                }
                $finalTypesPerCategorie[$category] = intdiv($finalType, 2);

            }
            if ($maxFinaleType == 1) {
                break;
            }
            $maxFinaleType = intdiv($maxFinaleType, 2);
        }

    }

    private function createDummyTeams($category, $winnerVsLooser, $groupIdx, $finaleType)
    {
        $groups = Group::where('category_id', $category)->get();
        if ($winnerVsLooser && $finaleType > 1) {
            $winner = $groups->get($groupIdx);
            $looser = $groupIdx % 2 == 0 ? $groups->get($groupIdx + 1) : $groups->get($groupIdx - 1);
            $dummyTeamOne = Team::create([
                'name' => "Gewinner {$winner->name}",
                'category_id' => $category,
                'dummy' => true,
                'temporary' => $this->temporary,
            ]);
            $dummyTeamTwo = Team::create([
                'name' => "Verlierer {$looser->name}",
                'category_id' => $category,
                'dummy' => true,
                'temporary' => $this->temporary,
            ]);

            return [$dummyTeamOne, $dummyTeamTwo];
        } elseif ($finaleType == 1 && $groups->count() == 0) {
            $categoryName = Category::find($category)->name;
            $dummyTeamOne = Team::create([
                'name' => "1. Tabelle {$categoryName}",
                'category_id' => $category,
                'dummy' => true,
                'temporary' => $this->temporary,
            ]);
            $dummyTeamTwo = Team::create([
                'name' => "1. Tabelle {$categoryName}",
                'category_id' => $category,
                'dummy' => true,
                'temporary' => $this->temporary,
            ]);

        } else {
            $finalCount = $groupIdx * 2 + 1;
            $dummyTeamOne = Team::create([
                'name' => "Gewinner {$this->finalsDict[$finaleType * 2]} {$finalCount}",
                'category_id' => $category,
                'dummy' => true,
                'temporary' => $this->temporary,
            ]);
            $finalCount++;
            $dummyTeamTwo = Team::create([
                'name' => "Gewinner {$this->finalsDict[$finaleType * 2]} {$finalCount}",
                'category_id' => $category,
                'dummy' => true,
                'temporary' => $this->temporary,
            ]);
        }

        return [$dummyTeamOne, $dummyTeamTwo];
    }

    private function createPlayForThirdGames()
    {
        if (! $this->options->play_for_third_place) {
            return;
        }
        $categories = Category::all();
        [$timeslot, $hall] = $this->getTimeAndHallSlot(newSlot: true);
        foreach ($categories as $category) {
            $multipleGroups = Group::where('category_id', $category->id)->count() > 1;
            $dummyTeamOne = Team::create([
                'name' => $multipleGroups ? "Verlierer Halbfinale {$category->name}" : "3. Tabelle {$category->name}",
                'category_id' => $category->id,
                'dummy' => true,
                'temporary' => $this->temporary,
            ]);
            $dummyTeamTwo = Team::create([
                'name' => $multipleGroups ? "Verlierer Halbfinale {$category->name}" : "4. Tabelle {$category->name}",
                'category_id' => $category->id,
                'dummy' => true,
                'temporary' => $this->temporary,
            ]);
            Game::create([
                'team_a_id' => $dummyTeamOne->id,
                'team_b_id' => $dummyTeamTwo->id,
                'category_id' => $category->id,
                'play_for_third' => true,
                'temporary' => $this->temporary,
                'timeslot_id' => $timeslot->id,
                'hall_id' => $hall->id,
                'finale_type' => 1,
            ]);
            [$timeslot, $hall] = $this->getTimeAndHallSlot(newSlot: false);

        }
        $this->getTimeAndHallSlot(newSlot: true);

    }

    private function createTimeTable($queue)
    {
        $carry = [];

        $currentTimeSlot = null;
        $teamsInCurrentSlot = [];
        $preferCarry = false;

        $stallCount = 0;

        $stallThresholdForSlot = 0;

        while (! empty($queue) || ! empty($carry)) {

            if ($stallThresholdForSlot > 0 && $stallCount >= $stallThresholdForSlot) {
                [$timeSlot, $hall] = $this->getTimeAndHallSlot(newSlot: true);
                $currentTimeSlot = $timeSlot;
                $teamsInCurrentSlot = [];
                $preferCarry = ! empty($carry);
                $stallCount = 0;
                $stallThresholdForSlot = count($queue) + count($carry);
            }

            [$timeSlot, $hall] = $this->getTimeAndHallSlot();

            if ($currentTimeSlot?->id !== $timeSlot->id) {
                $currentTimeSlot = $timeSlot;
                $teamsInCurrentSlot = [];
                $preferCarry = ! empty($carry);
                $stallCount = 0;
                $stallThresholdForSlot = count($queue) + count($carry);
            }

            if ($preferCarry && ! empty($carry)) {
                $game = array_shift($carry);
                if (empty($carry)) {
                    $preferCarry = false;
                }
            } else {
                $game = ! empty($queue) ? array_shift($queue) : array_shift($carry);
                if (! $game) {
                    break;
                }
            }

            if (
                in_array($game->team_a_id, $teamsInCurrentSlot, true) ||
                in_array($game->team_b_id, $teamsInCurrentSlot, true)
            ) {
                $carry[] = $game;
                $stallCount++;

                continue;
            }

            $teamsInCurrentSlot[] = $game->team_a_id;
            $teamsInCurrentSlot[] = $game->team_b_id;

            $game->timeslot_id = $timeSlot->id;
            $game->hall_id = $hall->id;
            $game->save();

            $stallCount = 0;
            $stallThresholdForSlot = count($queue) + count($carry);
        }
    }

    public function getTimeAndHallSlot($newSlot = false)
    {
        $lastTimeSlot = Timeslot::orderBy('end_time', 'desc')->where('temporary', $this->temporary)->first();
        $halls = Hall::all();
        if (! $lastTimeSlot) {
            $lastTimeSlot = Timeslot::create([
                'start_time' => $this->options->start_time,
                'end_time' => $this->options->start_time->copy()->addMinutes($this->options->game_duration),
                'temporary' => $this->temporary,
            ]);
        }
        foreach ($halls as $hall) {
            $game = Game::where('hall_id', $hall->id)
                ->where('timeslot_id', $lastTimeSlot->id)
                ->where('temporary', $this->temporary)->first();
            if (! $game && ! $newSlot) {
                return [$lastTimeSlot, $hall];
            }
        }

        $newTimeSlot = Timeslot::create([
            'start_time' => $lastTimeSlot->end_time->copy()->addMinutes($this->options->break_duration),
            'end_time' => $lastTimeSlot->end_time->copy()->addMinutes($this->options->game_duration + $this->options->break_duration),
            'temporary' => $this->temporary,
        ]);

        return [$newTimeSlot, $halls->first()];

    }

    private function organizeCategoryMatches($category)
    {
        $teams = Team::where('category_id', $category->id)->where('dummy', false)->get();
        $shuffledTeams = $teams->shuffle();
        $totalTeams = $shuffledTeams->count();
        $teamsPerSubgroup = intdiv($totalTeams, $category->subgroups);
        $remainingTeams = $totalTeams % $category->subgroups;

        $teamPointer = 0;
        $scheduledGames = [];

        for ($i = 0; $i < $category->subgroups; $i++) {
            $currentSubgroupSize = $teamsPerSubgroup + ($remainingTeams > 0 ? 1 : 0);
            $remainingTeams--;

            $subgroupTeams = $shuffledTeams->slice($teamPointer, $currentSubgroupSize);
            $teamPointer += $currentSubgroupSize;
            if ($category->subgroups > 1) {

                $currentGroup = Group::create([
                    'category_id' => $category->id,
                    'name' => chr(65 + $i),
                    'temporary' => $this->temporary,

                ]);
                foreach ($subgroupTeams as $team) {
                    $team->group_id = $currentGroup->id;
                    $team->save();
                }
            } else {
                $currentGroup = null;
            }

            $subGroupGames = $this->scheduleRoundRobin($subgroupTeams, $category, $currentGroup);
            $scheduledGames = array_merge($scheduledGames, $subGroupGames);
        }

        return $scheduledGames;
    }

    private function zipMergeGames($gamesByCategory)
    {
        $mergedGames = [];
        $hasGames = true;
        while ($hasGames) {
            $hasGames = false;
            foreach ($gamesByCategory as $category => $games) {
                if (empty($games)) {
                    continue;
                }
                $hasGames = true;
                $mergedGames[] = array_shift($gamesByCategory[$category]);
            }
        }

        return $mergedGames;
    }

    private function scheduleRoundRobin($teams, $category, $group)
    {
        $games = [];
        $teamCount = $teams->count();

        $hasBye = $teamCount % 2 != 0;
        if ($hasBye) {
            $teams = $teams->push(null);
            $teamCount++;
        }

        if ($teamCount < 2) {
            return;
        }

        $rounds = $teamCount - 1;
        $half = $teamCount / 2;

        $fixedTeam = $teams->first();
        $rotatingTeams = $teams->slice(1)->values();

        for ($round = 0; $round < $rounds; $round++) {

            if ($fixedTeam && $rotatingTeams->isNotEmpty() && $rotatingTeams->first()) {
                $games[] = Game::create([
                    'team_a_id' => $fixedTeam->id,
                    'team_b_id' => $rotatingTeams->first()->id,
                    'category_id' => $category->id,
                    'temporary' => $this->temporary,
                ]);
            }

            for ($i = 1; $i < $half; $i++) {
                $teamA = $rotatingTeams->get($i);
                $teamB = $rotatingTeams->get($rotatingTeams->count() - $i);

                if ($teamA && $teamB) {
                    $games[] = Game::create([
                        'team_a_id' => $teamA->id,
                        'team_b_id' => $teamB->id,
                        'category_id' => $category->id,
                        'temporary' => $this->temporary,
                    ]);
                }
            }

            $rotatingTeams = $rotatingTeams->slice(0, -1)->prepend($rotatingTeams->last());
        }

        return $games;
    }

    public function findTournamentConflicts()
    {
        $timeslots = Timeslot::where('temporary', $this->temporary)->orderBy('start_time')->get();
        $halls = Hall::all();
        foreach ($timeslots as $timeslot) {
            $numberOfGamesInTimeslot = Game::where('timeslot_id', $timeslot->id)->where('temporary', $this->temporary)->count();
            $gamesInTimeSlot = Game::where('timeslot_id', $timeslot->id)->where('temporary', $this->temporary)->get();
            $teamsInTimeslot = [];
            foreach ($gamesInTimeSlot as $game) {
                $teamsInTimeslot[] = $game->team_a_id;
                $teamsInTimeslot[] = $game->team_b_id;
            }
            $numbersOfDifferentTeamsInTimeslot = count(array_unique($teamsInTimeslot));

            if ($numberOfGamesInTimeslot * 2 == $numbersOfDifferentTeamsInTimeslot) {
                continue;
            }

            return "Conflict detected in timeslot {$timeslot->id} at {$timeslot->start_time->format('H:i')} - {$timeslot->end_time->format('H:i')}. {$numberOfGamesInTimeslot} games scheduled with only {$numbersOfDifferentTeamsInTimeslot} different teams.";
        }

    }
}
