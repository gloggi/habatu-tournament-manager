<?php

namespace Tests\Integration;

use App\Models\Category;
use App\Models\Game;
use App\Models\Group;
use App\Models\Hall;
use App\Models\Option;
use App\Models\Section;
use App\Models\Team;
use App\Models\Timeslot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TournamentControllerTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------
    //  HELPERS
    // ------------------------------------------------------------------

    private function makeSpecs(array $overrides = []): array
    {
        return array_merge([
            'start_time'           => '08:00',
            'game_duration'        => 20,
            'break_duration'       => 5,
            'round_robin'          => true,
            'group_phase'          => false,
            'groups_per_category'  => [],
            'play_for_third_place' => false,
        ], $overrides);
    }

    private function seedOption(array $overrides = []): Option
    {
        return Option::factory()->create($overrides);
    }

    private function seedTeamsForCategory(Category $category, int $count): \Illuminate\Database\Eloquent\Collection
    {
        $teams = collect();
        for ($i = 0; $i < $count; $i++) {
            $teams->push(
                Team::factory()->create([
                    'category_id' => $category->id,
                    'dummy'       => false,
                    'temporary'   => false,
                ])
            );
        }

        return new \Illuminate\Database\Eloquent\Collection($teams->all());
    }

    // ------------------------------------------------------------------
    //  SPECS
    // ------------------------------------------------------------------

    public function test_specs_returns_current_tournament_options(): void
    {
        $this->seedOption([
            'start_time'           => '09:30',
            'game_duration'        => 15,
            'break_duration'       => 3,
            'round_robin'          => true,
            'group_phase'          => false,
            'play_for_third_place' => true,
        ]);

        $category = Category::factory()->create(['subgroups' => 2]);

        $response = $this->getJson('/api/tournament/specs');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'start_time'           => '09:30',
                'game_duration'        => 15,
                'break_duration'       => 3,
                'round_robin'          => true,
                'group_phase'          => false,
                'play_for_third_place' => true,
            ]);

        $gpc = $response->json('groups_per_category');
        $this->assertArrayHasKey((string) $category->id, $gpc);
        $this->assertEquals(2, $gpc[(string) $category->id]);
    }

    public function test_specs_includes_all_categories_in_groups_per_category(): void
    {
        $this->seedOption();

        $cat1 = Category::factory()->create(['subgroups' => 1]);
        $cat2 = Category::factory()->create(['subgroups' => 4]);

        $response = $this->getJson('/api/tournament/specs');

        $response->assertOk();
        $gpc = $response->json('groups_per_category');
        $this->assertCount(2, $gpc);
        $this->assertEquals(1, $gpc[(string) $cat1->id]);
        $this->assertEquals(4, $gpc[(string) $cat2->id]);
    }

    // ------------------------------------------------------------------
    //  CALCULATE
    // ------------------------------------------------------------------

    public function test_calculate_rejects_missing_required_fields(): void
    {
        $this->seedOption();

        $response = $this->postJson('/api/tournament/calculate', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'start_time',
                'game_duration',
                'break_duration',
                'round_robin',
                'group_phase',
                'play_for_third_place',
            ]);
    }

    public function test_calculate_rejects_invalid_start_time_format(): void
    {
        $this->seedOption();

        $response = $this->postJson('/api/tournament/calculate', $this->makeSpecs([
            'start_time' => '25:99',
        ]));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['start_time']);
    }

    public function test_calculate_rejects_game_duration_zero(): void
    {
        $this->seedOption();

        $response = $this->postJson('/api/tournament/calculate', $this->makeSpecs([
            'game_duration' => 0,
        ]));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['game_duration']);
    }

    public function test_calculate_rejects_negative_break_duration(): void
    {
        $this->seedOption();

        $response = $this->postJson('/api/tournament/calculate', $this->makeSpecs([
            'break_duration' => -1,
        ]));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['break_duration']);
    }

    public function test_calculate_updates_option_row(): void
    {
        $this->seedOption();
        Hall::factory()->count(1)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 2);

        $response = $this->postJson('/api/tournament/calculate', $this->makeSpecs([
            'start_time'           => '10:00',
            'game_duration'        => 25,
            'break_duration'       => 10,
            'round_robin'          => false,
            'group_phase'          => true,
            'play_for_third_place' => true,
            'groups_per_category'  => [$category->id => 1],
        ]));

        $response->assertOk();

        $option = Option::first();
        $this->assertEquals(25, $option->game_duration);
        $this->assertEquals(10, $option->break_duration);
        $this->assertFalse($option->round_robin);
        $this->assertTrue($option->play_for_third_place);
    }

    public function test_calculate_forces_subgroups_to_one_when_round_robin(): void
    {
        $this->seedOption();

        $cat = Category::factory()->create(['subgroups' => 4]);

        $this->postJson('/api/tournament/calculate', $this->makeSpecs([
            'round_robin'         => true,
            'group_phase'         => false,
            'groups_per_category' => [$cat->id => 4],
        ]));

        $cat->refresh();
        $this->assertEquals(1, $cat->subgroups);
    }

    public function test_calculate_rejects_non_power_of_two_subgroups(): void
    {
        $this->seedOption();

        $cat = Category::factory()->create(['subgroups' => 1]);

        $this->postJson('/api/tournament/calculate', $this->makeSpecs([
            'round_robin'         => false,
            'group_phase'         => true,
            'groups_per_category' => [$cat->id => 3],
        ]));

        $cat->refresh();
        $this->assertEquals(1, $cat->subgroups);
    }

    public function test_calculate_clamps_subgroups_zero_to_one(): void
    {
        $this->seedOption();

        $cat = Category::factory()->create(['subgroups' => 2]);

        $this->postJson('/api/tournament/calculate', $this->makeSpecs([
            'round_robin'         => false,
            'group_phase'         => true,
            'groups_per_category' => [$cat->id => 0],
        ]));

        $cat->refresh();
        $this->assertEquals(1, $cat->subgroups);
    }

    public function test_calculate_round_robin_game_count_invariant_even_teams(): void
    {
        $this->seedOption();
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 4);

        $response = $this->postJson('/api/tournament/calculate', $this->makeSpecs([
            'round_robin'          => true,
            'group_phase'          => false,
            'play_for_third_place' => false,
        ]));

        $response->assertOk();

        $groupPhaseGames = Game::where('temporary', true)
            ->whereNull('finale_type')
            ->where('category_id', $category->id)
            ->count();

        $this->assertEquals(6, $groupPhaseGames);
    }

    public function test_calculate_round_robin_game_count_invariant_odd_teams(): void
    {
        $this->seedOption();
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 3);

        $response = $this->postJson('/api/tournament/calculate', $this->makeSpecs([
            'round_robin'          => true,
            'group_phase'          => false,
            'play_for_third_place' => false,
        ]));

        $response->assertOk();

        $groupPhaseGames = Game::where('temporary', true)
            ->whereNull('finale_type')
            ->where('category_id', $category->id)
            ->count();

        $this->assertEquals(3, $groupPhaseGames);
    }

    public function test_calculate_single_team_produces_no_group_games(): void
    {
        $this->seedOption();
        Hall::factory()->count(1)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 1);

        $response = $this->postJson('/api/tournament/calculate', $this->makeSpecs());

        $response->assertOk();

        $games = Game::where('temporary', true)
            ->whereNull('finale_type')
            ->where('category_id', $category->id)
            ->count();

        $this->assertEquals(0, $games);
    }

    public function test_calculate_round_robin_every_pair_plays_exactly_once(): void
    {
        $this->seedOption();
        Hall::factory()->count(3)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $teams = $this->seedTeamsForCategory($category, 5);

        $this->postJson('/api/tournament/calculate', $this->makeSpecs([
            'play_for_third_place' => false,
        ]));

        $games = Game::where('temporary', true)
            ->whereNull('finale_type')
            ->where('category_id', $category->id)
            ->get();

        $pairs = [];
        foreach ($games as $game) {
            $pair = [min($game->team_a_id, $game->team_b_id), max($game->team_a_id, $game->team_b_id)];
            $key  = implode('-', $pair);
            $pairs[$key] = ($pairs[$key] ?? 0) + 1;
        }

        $teamIds = $teams->pluck('id')->sort()->values()->all();
        $expectedPairCount = 0;
        for ($i = 0; $i < count($teamIds); $i++) {
            for ($j = $i + 1; $j < count($teamIds); $j++) {
                $key = $teamIds[$i] . '-' . $teamIds[$j];
                $this->assertArrayHasKey($key, $pairs);
                $this->assertEquals(1, $pairs[$key]);
                $expectedPairCount++;
            }
        }

        $this->assertCount($expectedPairCount, $pairs);
    }

    public function test_calculate_no_team_plays_twice_in_same_timeslot(): void
    {
        $this->seedOption();
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 6);

        $this->postJson('/api/tournament/calculate', $this->makeSpecs([
            'play_for_third_place' => false,
        ]));

        $timeslots = Timeslot::where('temporary', true)->get();
        foreach ($timeslots as $timeslot) {
            $games = Game::where('temporary', true)
                ->where('timeslot_id', $timeslot->id)
                ->get();

            $teamIds = [];
            foreach ($games as $game) {
                $teamIds[] = $game->team_a_id;
                $teamIds[] = $game->team_b_id;
            }

            $this->assertEquals(
                count($teamIds),
                count(array_unique($teamIds)),
                "Timeslot {$timeslot->id} has a team scheduled in multiple games"
            );
        }
    }

    public function test_calculate_timeslots_are_strictly_monotonic(): void
    {
        $this->seedOption();
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 4);

        $this->postJson('/api/tournament/calculate', $this->makeSpecs());

        $timeslots = Timeslot::where('temporary', true)
            ->orderBy('start_time')
            ->get();

        for ($i = 1; $i < $timeslots->count(); $i++) {
            $this->assertTrue($timeslots[$i]->start_time->gt($timeslots[$i - 1]->start_time));
            $this->assertTrue($timeslots[$i]->start_time->gte($timeslots[$i - 1]->end_time));
        }
    }

    public function test_calculate_timeslot_duration_matches_game_duration(): void
    {
        $this->seedOption();
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 4);

        $gameDuration = 15;
        $this->postJson('/api/tournament/calculate', $this->makeSpecs([
            'game_duration' => $gameDuration,
        ]));

        $timeslots = Timeslot::where('temporary', true)->get();
        foreach ($timeslots as $timeslot) {
            $duration = $timeslot->start_time->diffInMinutes($timeslot->end_time);
            $this->assertEquals($gameDuration, $duration);
        }
    }

    public function test_calculate_multi_category_total_game_count(): void
    {
        $this->seedOption();
        Hall::factory()->count(3)->create();

        $cat1 = Category::factory()->create(['subgroups' => 1]);
        $cat2 = Category::factory()->create(['subgroups' => 1]);

        $this->seedTeamsForCategory($cat1, 3);
        $this->seedTeamsForCategory($cat2, 4);

        $this->postJson('/api/tournament/calculate', $this->makeSpecs([
            'play_for_third_place' => false,
        ]));

        $groupGames = Game::where('temporary', true)
            ->whereNull('finale_type')
            ->count();

        $this->assertEquals(9, $groupGames);
    }

    // ------------------------------------------------------------------
    //  CREATE
    // ------------------------------------------------------------------

    public function test_create_persists_non_temporary_games(): void
    {
        $this->seedOption();
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 4);

        $response = $this->postJson('/api/tournament/create', $this->makeSpecs([
            'play_for_third_place' => false,
        ]));

        $response->assertOk()
            ->assertJsonFragment(['message' => 'Tournament created successfully']);

        $tempGames = Game::where('temporary', true)->count();
        $this->assertEquals(0, $tempGames);

        $nonTempGames = Game::where('temporary', false)->count();
        $this->assertGreaterThan(0, $nonTempGames);
    }

    public function test_create_is_idempotent_replaces_previous_tournament(): void
    {
        $this->seedOption();
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 3);

        $specs = $this->makeSpecs(['play_for_third_place' => false]);

        $this->postJson('/api/tournament/create', $specs);
        $firstCount = Game::where('temporary', false)->count();

        $this->postJson('/api/tournament/create', $specs);
        $secondCount = Game::where('temporary', false)->count();

        $this->assertEquals($firstCount, $secondCount);
        $this->assertEquals(0, Game::where('temporary', true)->count());
        $this->assertEquals(0, Timeslot::where('temporary', true)->count());
    }

    // ------------------------------------------------------------------
    //  TABLE
    // ------------------------------------------------------------------

    public function test_table_returns_grid_structure_of_timeslots_and_halls(): void
    {
        $this->seedOption();
        Hall::factory()->create(['name' => 'Halle A']);
        Hall::factory()->create(['name' => 'Halle B']);
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 4);

        $this->postJson('/api/tournament/create', $this->makeSpecs([
            'play_for_third_place' => false,
        ]));

        $response = $this->getJson('/api/tournament/table');
        $response->assertOk();

        $table = $response->json();
        $this->assertIsArray($table);
        $this->assertNotEmpty($table);

        foreach ($table as $timeslotKey => $halls) {
            $this->assertArrayHasKey('Halle A', $halls);
            $this->assertArrayHasKey('Halle B', $halls);
            foreach ($halls as $hallName => $hallData) {
                $this->assertArrayHasKey('games', $hallData);
                $this->assertArrayHasKey('slot_info', $hallData);
                $this->assertArrayHasKey('hall_id', $hallData['slot_info']);
                $this->assertArrayHasKey('timeslot_id', $hallData['slot_info']);
            }
        }
    }

    public function test_table_excludes_temporary_games_and_timeslots(): void
    {
        $this->seedOption();
        Hall::factory()->count(1)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 3);

        $this->postJson('/api/tournament/create', $this->makeSpecs([
            'play_for_third_place' => false,
        ]));

        $tempTimeslot = Timeslot::factory()->temporary()->create([
            'start_time' => '23:00',
            'end_time'   => '23:20',
        ]);
        $tempTeamA = Team::factory()->create(['category_id' => $category->id]);
        $tempTeamB = Team::factory()->create(['category_id' => $category->id]);
        Game::factory()->temporary()->create([
            'timeslot_id' => $tempTimeslot->id,
            'category_id' => $category->id,
            'team_a_id'   => $tempTeamA->id,
            'team_b_id'   => $tempTeamB->id,
        ]);

        $response = $this->getJson('/api/tournament/table');
        $response->assertOk();

        $table = $response->json();
        foreach (array_keys($table) as $tsKey) {
            $this->assertStringNotContainsString('23:00', $tsKey);
        }
    }

    // ------------------------------------------------------------------
    //  RANKING
    // ------------------------------------------------------------------

    public function test_ranking_score_conservation_invariant(): void
    {
        $this->seedOption();
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 4);

        $this->postJson('/api/tournament/create', $this->makeSpecs([
            'play_for_third_place' => false,
        ]));

        $games = Game::where('temporary', false)
            ->whereNull('finale_type')
            ->where('category_id', $category->id)
            ->get();

        $scores = [[10, 5], [3, 3], [7, 2], [1, 8], [4, 4], [6, 6]];
        foreach ($games as $i => $game) {
            $s = $scores[$i % count($scores)];
            $game->update([
                'played'        => true,
                'points_team_a' => $s[0],
                'points_team_b' => $s[1],
            ]);
        }

        $response = $this->getJson('/api/tournament/ranking');
        $response->assertOk();

        $ranking = $response->json();
        $this->assertNotEmpty($ranking);

        foreach ($ranking as $categoryRanking) {
            foreach ($categoryRanking['groups'] as $group) {
                foreach ($group['ranking'] as $entry) {
                    $this->assertEquals(
                        $entry['matches_played'],
                        $entry['wins'] + $entry['draws'] + $entry['losses']
                    );

                    $expectedPoints = 3 * $entry['wins'] + 1 * $entry['draws'];
                    $this->assertEquals($expectedPoints, $entry['points']);

                    $this->assertEquals(
                        $entry['goals_scored'] - $entry['goals_conceded'],
                        $entry['goals_difference']
                    );
                }
            }
        }
    }

    public function test_ranking_is_sorted_by_points_descending(): void
    {
        $this->seedOption();
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 4);

        $this->postJson('/api/tournament/create', $this->makeSpecs([
            'play_for_third_place' => false,
        ]));

        $games = Game::where('temporary', false)
            ->whereNull('finale_type')
            ->where('category_id', $category->id)
            ->get();

        foreach ($games as $game) {
            $game->update([
                'played'        => true,
                'points_team_a' => 10,
                'points_team_b' => 5,
            ]);
        }

        $response = $this->getJson('/api/tournament/ranking');
        $response->assertOk();

        $ranking = $response->json();
        foreach ($ranking as $catRanking) {
            foreach ($catRanking['groups'] as $group) {
                $points = array_column($group['ranking'], 'points');
                for ($i = 1; $i < count($points); $i++) {
                    $this->assertGreaterThanOrEqual($points[$i], $points[$i - 1]);
                }
            }
        }
    }

    public function test_ranking_has_sequential_ranks(): void
    {
        $this->seedOption();
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 3);

        $this->postJson('/api/tournament/create', $this->makeSpecs([
            'play_for_third_place' => false,
        ]));

        Game::where('temporary', false)->whereNull('finale_type')
            ->update(['played' => true, 'points_team_a' => 5, 'points_team_b' => 3]);

        $response = $this->getJson('/api/tournament/ranking');
        $response->assertOk();

        foreach ($response->json() as $catRanking) {
            foreach ($catRanking['groups'] as $group) {
                $ranks = array_column($group['ranking'], 'rank');
                $expected = range(1, count($ranks));
                $this->assertEquals($expected, $ranks);
            }
        }
    }

    public function test_ranking_excludes_dummy_teams(): void
    {
        $this->seedOption();
        Hall::factory()->count(1)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 3);

        Team::factory()->dummy()->create(['category_id' => $category->id]);

        $this->postJson('/api/tournament/create', $this->makeSpecs([
            'play_for_third_place' => false,
        ]));

        $response = $this->getJson('/api/tournament/ranking');
        $response->assertOk();

        foreach ($response->json() as $catRanking) {
            foreach ($catRanking['groups'] as $group) {
                foreach ($group['ranking'] as $entry) {
                    $this->assertFalse((bool) $entry['team']['dummy']);
                }
            }
        }
    }

    // ------------------------------------------------------------------
    //  FINALS RANKING
    // ------------------------------------------------------------------

    public function test_finals_ranking_returns_empty_when_unplayed_finals_exist(): void
    {
        $this->seedOption();
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 4);

        $this->postJson('/api/tournament/create', $this->makeSpecs([
            'play_for_third_place' => false,
        ]));

        $hasUnplayedFinal = Game::where('finale_type', '>', 0)
            ->where('played', false)
            ->exists();
        $this->assertTrue($hasUnplayedFinal);

        $response = $this->getJson('/api/tournament/finals-ranking');
        $response->assertOk()->assertExactJson([]);
    }

    public function test_finals_ranking_returns_data_when_all_finals_played(): void
    {
        $this->seedOption();
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 4);

        $this->postJson('/api/tournament/create', $this->makeSpecs([
            'play_for_third_place' => false,
        ]));

        Game::where('finale_type', '>', 0)->update([
            'played'        => true,
            'points_team_a' => 10,
            'points_team_b' => 5,
        ]);

        $response = $this->getJson('/api/tournament/finals-ranking');
        $response->assertOk();

        $data = $response->json();
        $this->assertNotEmpty($data);
        $this->assertEquals($category->name, $data[0]['category_name']);
    }

    // ------------------------------------------------------------------
    //  REFEREE TABLE
    // ------------------------------------------------------------------

    public function test_referee_table_returns_error_when_unauthenticated(): void
    {
        $this->seedOption();

        $response = $this->getJson('/api/tournament/referee-table');

        $response->assertUnauthorized();
    }

    public function test_referee_table_returns_filtered_games_for_authenticated_referee(): void
    {
        $this->seedOption();
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 4);

        $this->postJson('/api/tournament/create', $this->makeSpecs([
            'play_for_third_place' => false,
        ]));

        $referee = User::factory()->create(['role' => 'referee']);
        $game = Game::where('temporary', false)->whereNull('finale_type')->first();
        $game->referees()->attach($referee->id);

        Sanctum::actingAs($referee);

        $response = $this->getJson('/api/tournament/referee-table');
        $response->assertOk();

        $table = $response->json();
        $hasRefereeGame = false;
        foreach ($table as $timeslotKey => $halls) {
            foreach ($halls as $hallName => $hallData) {
                if (!empty($hallData['games'])) {
                    $hasRefereeGame = true;
                    foreach ($hallData['games'] as $g) {
                        $this->assertEquals($game->id, $g['id']);
                    }
                }
            }
        }

        $this->assertTrue($hasRefereeGame);
    }

    // ------------------------------------------------------------------
    //  TEAM TABLE
    // ------------------------------------------------------------------

    public function test_team_table_returns_error_when_unauthenticated(): void
    {
        $this->seedOption();

        $response = $this->getJson('/api/tournament/team-table');

        $response->assertUnauthorized();
    }

    public function test_team_table_returns_filtered_games_for_authenticated_users_team(): void
    {
        $this->seedOption();
        Hall::factory()->count(2)->create();
        $section = Section::factory()->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $teams = $this->seedTeamsForCategory($category, 4);

        $this->postJson('/api/tournament/create', $this->makeSpecs([
            'play_for_third_place' => false,
        ]));

        $myTeam = $teams->first();
        $user = User::factory()->create([
            'team_id'    => $myTeam->id,
            'section_id' => $section->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/tournament/team-table');
        $response->assertOk();

        $table = $response->json();
        foreach ($table as $timeslotKey => $halls) {
            foreach ($halls as $hallName => $hallData) {
                foreach ($hallData['games'] as $g) {
                    $this->assertTrue(
                        $g['team_a_id'] == $myTeam->id || $g['team_b_id'] == $myTeam->id
                    );
                }
            }
        }
    }

    public function test_team_table_returns_empty_when_user_has_no_team(): void
    {
        $this->seedOption();
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 4);

        $this->postJson('/api/tournament/create', $this->makeSpecs([
            'play_for_third_place' => false,
        ]));

        $userWithoutTeam = User::factory()->create(['team_id' => null]);
        Sanctum::actingAs($userWithoutTeam);

        $response = $this->getJson('/api/tournament/team-table');
        $response->assertOk();
        $this->assertEquals([], $response->json());
    }

    // ------------------------------------------------------------------
    //  CONFLICTS
    // ------------------------------------------------------------------

    public function test_conflicts_returns_null_for_conflict_free_tournament(): void
    {
        $this->seedOption();
        Hall::factory()->count(3)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 4);

        $this->postJson('/api/tournament/create', $this->makeSpecs([
            'play_for_third_place' => false,
        ]));

        $response = $this->getJson('/api/tournament/conflicts');
        $response->assertOk();

        $body = $response->json();
        $this->assertTrue($body === null || $body === []);
    }

    public function test_conflicts_detects_duplicate_team_in_timeslot(): void
    {
        $this->seedOption();
        $hall1 = Hall::factory()->create();
        $hall2 = Hall::factory()->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $timeslot = Timeslot::factory()->create(['temporary' => false]);
        $teamA = Team::factory()->create(['category_id' => $category->id, 'dummy' => false]);
        $teamB = Team::factory()->create(['category_id' => $category->id, 'dummy' => false]);
        $teamC = Team::factory()->create(['category_id' => $category->id, 'dummy' => false]);

        Game::factory()->create([
            'team_a_id'   => $teamA->id,
            'team_b_id'   => $teamB->id,
            'category_id' => $category->id,
            'hall_id'     => $hall1->id,
            'timeslot_id' => $timeslot->id,
            'temporary'   => false,
        ]);
        Game::factory()->create([
            'team_a_id'   => $teamA->id,
            'team_b_id'   => $teamC->id,
            'category_id' => $category->id,
            'hall_id'     => $hall2->id,
            'timeslot_id' => $timeslot->id,
            'temporary'   => false,
        ]);

        $response = $this->getJson('/api/tournament/conflicts');
        $response->assertOk();

        $conflict = $response->json();
        $this->assertNotNull($conflict);
        $this->assertStringContainsString('Conflict', $conflict);
    }

    // ------------------------------------------------------------------
    //  NEW TIMESLOT
    // ------------------------------------------------------------------

    public function test_new_timeslot_appends_timeslot_after_last(): void
    {
        $this->seedOption([
            'game_duration'  => 20,
            'break_duration' => 5,
        ]);
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 4);

        $this->postJson('/api/tournament/create', $this->makeSpecs([
            'game_duration'        => 20,
            'break_duration'       => 5,
            'play_for_third_place' => false,
        ]));

        $timeslotCountBefore = Timeslot::where('temporary', false)->count();

        $response = $this->postJson('/api/tournament/new-timeslot');
        $response->assertOk();

        $timeslotCountAfter = Timeslot::where('temporary', false)->count();
        $this->assertEquals($timeslotCountBefore + 1, $timeslotCountAfter);
    }

    // ------------------------------------------------------------------
    //  GROUP PHASE
    // ------------------------------------------------------------------

    public function test_calculate_group_phase_creates_correct_subgroups(): void
    {
        $this->seedOption();
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 4);

        $this->postJson('/api/tournament/calculate', $this->makeSpecs([
            'round_robin'          => false,
            'group_phase'          => true,
            'groups_per_category'  => [$category->id => 2],
            'play_for_third_place' => false,
        ]));

        $groups = Group::where('category_id', $category->id)
            ->where('temporary', true)
            ->get();

        $this->assertEquals(2, $groups->count());

        $groupGames = Game::where('temporary', true)
            ->whereNull('finale_type')
            ->where('category_id', $category->id)
            ->count();

        $this->assertEquals(2, $groupGames);
    }

    public function test_calculate_group_phase_partitions_teams_exactly(): void
    {
        $this->seedOption();
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $teams = $this->seedTeamsForCategory($category, 6);

        $this->postJson('/api/tournament/calculate', $this->makeSpecs([
            'round_robin'          => false,
            'group_phase'          => true,
            'groups_per_category'  => [$category->id => 2],
            'play_for_third_place' => false,
        ]));

        $groups = Group::where('category_id', $category->id)
            ->where('temporary', true)
            ->get();

        $assignedTeamIds = collect();
        foreach ($groups as $group) {
            $teamIdsInGroup = Team::where('group_id', $group->id)
                ->where('dummy', false)
                ->pluck('id');
            $overlap = $assignedTeamIds->intersect($teamIdsInGroup);
            $this->assertEmpty($overlap->all());
            $assignedTeamIds = $assignedTeamIds->merge($teamIdsInGroup);
        }

        $this->assertEquals(
            $teams->pluck('id')->sort()->values()->all(),
            $assignedTeamIds->sort()->values()->all()
        );
    }

    // ------------------------------------------------------------------
    //  FINALS
    // ------------------------------------------------------------------

    public function test_create_with_play_for_third_creates_third_place_games(): void
    {
        $this->seedOption(['play_for_third_place' => true]);
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 4);

        $this->postJson('/api/tournament/create', $this->makeSpecs([
            'play_for_third_place' => true,
        ]));

        $thirdPlaceGames = Game::where('temporary', false)
            ->where('play_for_third', true)
            ->where('category_id', $category->id)
            ->count();

        $this->assertEquals(1, $thirdPlaceGames);
    }

    public function test_create_with_play_for_third_exposes_stale_cache_bug(): void
    {
        $this->seedOption(['play_for_third_place' => false]);
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 4);

        $this->postJson('/api/tournament/create', $this->makeSpecs([
            'play_for_third_place' => true,
        ]));

        $thirdPlaceGames = Game::where('temporary', false)
            ->where('play_for_third', true)
            ->where('category_id', $category->id)
            ->count();

        $this->assertEquals(1, $thirdPlaceGames);
    }

    public function test_create_without_play_for_third_creates_no_third_place_games(): void
    {
        $this->seedOption();
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 4);

        $this->postJson('/api/tournament/create', $this->makeSpecs([
            'play_for_third_place' => false,
        ]));

        $thirdPlaceGames = Game::where('temporary', false)
            ->where('play_for_third', true)
            ->count();

        $this->assertEquals(0, $thirdPlaceGames);
    }

    public function test_create_finale_games_use_dummy_teams(): void
    {
        $this->seedOption();
        Hall::factory()->count(2)->create();
        $category = Category::factory()->create(['subgroups' => 1]);
        $this->seedTeamsForCategory($category, 4);

        $this->postJson('/api/tournament/create', $this->makeSpecs([
            'play_for_third_place' => false,
        ]));

        $finaleGames = Game::where('temporary', false)
            ->whereNotNull('finale_type')
            ->get();

        foreach ($finaleGames as $game) {
            $teamA = Team::find($game->team_a_id);
            $teamB = Team::find($game->team_b_id);

            $this->assertTrue((bool) $teamA->dummy);
            $this->assertTrue((bool) $teamB->dummy);
        }
    }
}
