<?php

namespace Tests\Integration;

use App\Models\Category;
use App\Models\Game;
use App\Models\Hall;
use App\Models\Section;
use App\Models\Team;
use App\Models\Timeslot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameControllerTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------
    //  INDEX
    // ------------------------------------------------------------------

    public function test_index_returns_non_finale_non_temporary_games_with_relations(): void
    {
        $games = Game::factory()->count(2)->create([
            'finale_type' => null,
            'temporary'   => false,
        ]);

        // These should be excluded by index query
        Game::factory()->finale(1)->create(['temporary' => false]);
        Game::factory()->temporary()->create(['finale_type' => null]);

        $response = $this->getJson('/api/games');

        $response
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'team_a_id',
                    'team_b_id',
                    'category_id',
                    'hall_id',
                    'timeslot_id',
                    'team_a',
                    'team_b',
                    'hall',
                    'timeslot',
                    'category',
                    'referees',
                ],
            ]);

        $returnedIds = collect($response->json())->pluck('id')->sort()->values()->all();
        $this->assertEquals($games->pluck('id')->sort()->values()->all(), $returnedIds);
    }

    // ------------------------------------------------------------------
    //  STORE
    // ------------------------------------------------------------------

    public function test_store_creates_game_and_returns_201(): void
    {
        $category = Category::factory()->create();
        $teamA    = Team::factory()->for($category)->create();
        $teamB    = Team::factory()->for($category)->create();
        $hall     = Hall::factory()->create();
        $timeslot = Timeslot::factory()->create();

        $payload = [
            'team_a_id'   => $teamA->id,
            'team_b_id'   => $teamB->id,
            'hall_id'     => $hall->id,
            'timeslot_id' => $timeslot->id,
            'category_id' => $category->id,
            'played'      => false,
        ];

        $response = $this->postJson('/api/games', $payload);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'team_a_id'   => $teamA->id,
                'team_b_id'   => $teamB->id,
                'hall_id'     => $hall->id,
                'timeslot_id' => $timeslot->id,
                'category_id' => $category->id,
            ]);

        $this->assertDatabaseHas('games', [
            'team_a_id'   => $teamA->id,
            'team_b_id'   => $teamB->id,
            'hall_id'     => $hall->id,
            'timeslot_id' => $timeslot->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_store_rejects_missing_required_foreign_keys(): void
    {
        $response = $this->postJson('/api/games', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'team_a_id',
                'team_b_id',
                'hall_id',
                'timeslot_id',
                'category_id',
            ]);
    }

    public function test_store_rejects_nonexistent_foreign_keys(): void
    {
        $payload = [
            'team_a_id'   => 99999,
            'team_b_id'   => 99999,
            'hall_id'     => 99999,
            'timeslot_id' => 99999,
            'category_id' => 99999,
        ];

        $response = $this->postJson('/api/games', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'team_a_id',
                'team_b_id',
                'hall_id',
                'timeslot_id',
                'category_id',
            ]);
    }

    // ------------------------------------------------------------------
    //  SHOW
    // ------------------------------------------------------------------

    public function test_show_returns_single_game_with_relations(): void
    {
        $sectionA = Section::factory()->create(['name' => 'Sektion Rot']);
        $sectionB = Section::factory()->create(['name' => 'Sektion Blau']);
        $teamA    = Team::factory()->for($sectionA)->create();
        $teamB    = Team::factory()->for($sectionB)->create();

        $game = Game::factory()->create([
            'team_a_id' => $teamA->id,
            'team_b_id' => $teamB->id,
        ]);

        $response = $this->getJson("/api/games/{$game->id}");

        $response
            ->assertOk()
            ->assertJsonStructure([
                'id',
                'team_a' => ['id', 'section' => ['id', 'name']],
                'team_b' => ['id', 'section' => ['id', 'name']],
                'hall' => ['id', 'name'],
                'timeslot' => ['id'],
                'category' => ['id', 'name'],
                'referees',
            ]);

        $this->assertEquals($game->id, $response->json('id'));
        $this->assertEquals('Sektion Rot', $response->json('team_a.section.name'));
        $this->assertEquals('Sektion Blau', $response->json('team_b.section.name'));
    }

    public function test_show_returns_404_for_nonexistent_game(): void
    {
        $response = $this->getJson('/api/games/99999');

        $response->assertNotFound();
    }

    // ------------------------------------------------------------------
    //  UPDATE
    // ------------------------------------------------------------------

    public function test_update_modifies_game_attributes(): void
    {
        $game    = Game::factory()->create();
        $newHall = Hall::factory()->create();

        $payload = [
            'hall_id' => $newHall->id,
        ];

        $response = $this->putJson("/api/games/{$game->id}", $payload);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'hall_id' => $newHall->id,
            ]);

        $this->assertDatabaseHas('games', [
            'id'      => $game->id,
            'hall_id' => $newHall->id,
        ]);
    }

    public function test_update_syncs_referees(): void
    {
        $game     = Game::factory()->create();
        $referee1 = User::factory()->create();
        $referee2 = User::factory()->create();

        $payload = [
            'referees' => [
                ['id' => $referee1->id],
                ['id' => $referee2->id],
            ],
        ];

        $response = $this->putJson("/api/games/{$game->id}", $payload);

        $response->assertOk();

        $this->assertDatabaseHas('game_user', [
            'game_id' => $game->id,
            'user_id' => $referee1->id,
        ]);
        $this->assertDatabaseHas('game_user', [
            'game_id' => $game->id,
            'user_id' => $referee2->id,
        ]);
        $this->assertCount(2, $game->fresh()->referees);
    }

    public function test_update_auto_sets_played_true_when_points_entered(): void
    {
        $game = Game::factory()->create([
            'played'        => false,
            'points_team_a' => 0,
            'points_team_b' => 0,
        ]);

        $payload = [
            'points_team_a' => 15,
            'points_team_b' => 12,
        ];

        $response = $this->putJson("/api/games/{$game->id}", $payload);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'played'        => true,
                'points_team_a' => 15,
                'points_team_b' => 12,
            ]);

        $this->assertDatabaseHas('games', [
            'id'            => $game->id,
            'played'        => true,
            'points_team_a' => 15,
            'points_team_b' => 12,
        ]);
    }

    public function test_update_returns_404_for_nonexistent_game(): void
    {
        $response = $this->putJson('/api/games/99999', [
            'points_team_a' => 10,
        ]);

        $response->assertNotFound();
    }

    // ------------------------------------------------------------------
    //  DESTROY
    // ------------------------------------------------------------------

    public function test_destroy_deletes_game_and_returns_success_message(): void
    {
        $game = Game::factory()->create();

        $response = $this->deleteJson("/api/games/{$game->id}");

        $response
            ->assertOk()
            ->assertJsonFragment(['message' => 'Game deleted successfully']);

        $this->assertDatabaseMissing('games', ['id' => $game->id]);
    }

    public function test_destroy_returns_404_for_nonexistent_game(): void
    {
        $response = $this->deleteJson('/api/games/99999');

        $response->assertNotFound();
    }
}
