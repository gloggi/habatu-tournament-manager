<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Game;
use App\Models\Group;
use App\Models\Option;
use App\Models\Team;

class FinaleService
{
    private $options;

    public function __construct()
    {
        $this->options = Option::first();
    }

    public function assignFinaleTeams()
    {
        $allCategories = Category::all();
        foreach ($allCategories as $category) {
            $unplayedGamesCount = Game::whereNull('finale_type')
                ->where('temporary', false)
                ->where('played', false)
                ->where('category_id', $category->id)
                ->count();
            if ($unplayedGamesCount == 0) {
                $this->assignFinaleTeamsToCategory($category);
            }

        }

    }

    private function assignFinaleTeamsToCategory($category)
    {
        $nextFinaleType = Game::where('category_id', $category->id)
            ->where('temporary', false)
            ->where('played', false)
            ->whereNotNull('finale_type')
            ->max('finale_type');
        $groups = Group::where('category_id', $category->id)->where('temporary', false)->get();
        if ($groups->count() > 0 && $nextFinaleType == $groups->count()) {
            $this->assignGroupWinnerAndLoosers($category, $groups, $nextFinaleType);
        } elseif ($groups->count() > 0) {
            $this->assignFinaleTeamsByCategory($category, $nextFinaleType);
        } elseif($nextFinaleType) {
            $this->assignFinaleTeamsZeroGroups($category, $nextFinaleType);
        }

    }

    private function assignGroupWinnerAndLoosers($category, $groups, $nextFinaleType)
    {
        $gamesToAssign = Game::where('category_id', $category->id)
            ->where('temporary', false)
            ->where('finale_type', $nextFinaleType)->get();
        for ($i = 0; $i < $nextFinaleType; $i++) {
            $firstGroup = $groups->get($i);
            $firstGroupTeams = Team::where('group_id', $firstGroup->id)
                ->where('temporary', false)->get();

            $winnerTeam = $this->rankTeams($firstGroupTeams)[0]['team'];
            $seccondGroup = $i % 2 == 0 ? $groups->get($i + 1) : $groups->get($i - 1);
            $seccondGroupTeams = Team::where('group_id', $seccondGroup->id)
                ->where('temporary', false)->get();
            $looserTeam = $this->rankTeams($seccondGroupTeams)[1]['team'];
            $gameToBeAssigned = $gamesToAssign->get($i);
            $gameToBeAssigned->team_a_id = $winnerTeam->id;
            $gameToBeAssigned->team_b_id = $looserTeam->id;
            $gameToBeAssigned->save();
        }

    }

    private function assignFinaleTeamsByCategory($category, $nextFinaleType)
    {
        if (! $nextFinaleType) {
            return;
        }
        $gamesToAssign = Game::where('category_id', $category->id)
            ->where('temporary', false)
            ->where('play_for_third', false)
            ->where('finale_type', $nextFinaleType)->get();
        $lastFinals = Game::where('category_id', $category->id)
            ->where('temporary', false)
            ->where('finale_type', $nextFinaleType * 2)->get();
        for ($i = 0; $i < $gamesToAssign->count(); $i++) {
            $finalOne = $lastFinals->get($i * 2);
            $winnerOne = $finalOne->points_team_a > $finalOne->points_team_b ? $finalOne->team_a_id : $finalOne->team_b_id;
            $finalTwo = $lastFinals->get($i * 2 + 1);
            $winnerTwo = $finalTwo->points_team_a > $finalTwo->points_team_b ? $finalTwo->team_a_id : $finalTwo->team_b_id;
            $gameToAssing = $gamesToAssign->get($i);
            $gameToAssing->team_a_id = $winnerOne;
            $gameToAssing->team_b_id = $winnerTwo;
            $gameToAssing->save();
        }
        if ($this->options->play_for_third_place && $nextFinaleType == 1) {
            $thirdPlaceGame = Game::where('category_id', $category->id)
                ->where('temporary', false)
                ->where('play_for_third', true)
                ->where('finale_type', $nextFinaleType)->first();
            $finalOne = $lastFinals->get(0);
            $looserOne = $finalOne->points_team_a < $finalOne->points_team_b ? $finalOne->team_a_id : $finalOne->team_b_id;
            $finalTwo = $lastFinals->get(1);
            $looserTwo = $finalTwo->points_team_a < $finalTwo->points_team_b ? $finalTwo->team_a_id : $finalTwo->team_b_id;
            $thirdPlaceGame->team_a_id = $looserOne;
            $thirdPlaceGame->team_b_id = $looserTwo;
            $thirdPlaceGame->save();
        }

    }

    public function assignFinaleTeamsZeroGroups($category, $nextFinaleType)
    {
        $final = Game::where('category_id', $category->id)
            ->where('temporary', false)
            ->where('play_for_third', false)
            ->where('finale_type', $nextFinaleType)->first();
        $playForThird = Game::where('category_id', $category->id)
            ->where('temporary', false)
            ->where('play_for_third', true)
            ->where('finale_type', $nextFinaleType)->first();
        $teams = Team::where('category_id', $category->id)
            ->where('temporary', false)->get();
        $rankedTeams = $this->rankTeams($teams);
        $final->team_a_id = $rankedTeams[0]['team']->id;
        $final->team_b_id = $rankedTeams[1]['team']->id;
        $final->save();
        if ($this->options->play_for_third_place) {
            $playForThird->team_a_id = $rankedTeams[2]['team']->id;
            $playForThird->team_b_id = $rankedTeams[3]['team']->id;
            $playForThird->save();
        }
    }

    public function rankTeams($teams)
    {
        $teamsRanking = [];
        foreach ($teams as $team) {
            $goalsConceded = 0;
            $goalsScored = 0;
            $gamesWon = 0;
            $gamesLost = 0;
            $gamesDrawn = 0;
            $team->points = 0;
            $games = Game::where('played', true)
                ->where('temporary', false)
                ->whereNull('finale_type')
                ->where(function ($query) use ($team) {
                    $query->where('team_a_id', $team->id)->orWhere('team_b_id', $team->id);
                })
                ->get();
            foreach ($games as $game) {
                if ($game->team_a_id == $team->id) {
                    $goalsScored += $game->points_team_a;
                    $goalsConceded += $game->points_team_b;
                    if ($game->points_team_a > $game->points_team_b) {
                        $team->points += 3;
                        $gamesWon++;
                    } elseif ($game->points_team_a == $game->points_team_b) {
                        $team->points += 1;
                        $gamesDrawn++;
                    } else {
                        $team->points += 0;
                        $gamesLost++;
                    }
                } else {
                    $goalsScored += $game->points_team_b;
                    $goalsConceded += $game->points_team_a;
                    if ($game->points_team_b > $game->points_team_a) {
                        $team->points += 3;
                        $gamesWon++;
                    } elseif ($game->points_team_a == $game->points_team_b) {
                        $team->points += 1;
                        $gamesDrawn++;
                    } else {
                        $team->points += 0;
                        $gamesLost++;
                    }
                }
            }
            $goalDifference = $goalsScored - $goalsConceded;
            $teamsRanking[] = [
                'rank' => 0,
                'team' => $team,
                'matches_played' => count($games),
                'wins' => $gamesWon,
                'draws' => $gamesDrawn,
                'losses' => $gamesLost,
                'goals_scored' => $goalsScored,
                'goals_conceded' => $goalsConceded,
                'goals_difference' => $goalDifference,
                'points' => $team->points,
            ];

        }
        

        usort($teamsRanking, function ($a, $b) {
            if ($a['points'] == $b['points']) {
                if ($a['goals_conceded'] == $b['goals_conceded']) {
                    return $b['goals_scored'] - $a['goals_scored'];
                }

                return $b['goals_conceded'] - $a['goals_conceded'];
            }

            return $b['points'] - $a['points'];
        });

        $rank = 1;
        foreach ($teamsRanking as $key => $team) {
            $teamsRanking[$key]['rank'] = $rank;
            $rank++;
        }

        return $teamsRanking;
    }

    public function rankTeamsInFinals($teams){
        $teamsRanking = [];
        $games = Game::where('played', true)
                ->where('temporary', false)
                ->whereNotNull('finale_type')
               ->where(function ($query) use ($team) {
                    $query->where('team_a_id', $team->id)->orWhere('team_b_id', $team->id);
                })
                ->get();

    }
}
