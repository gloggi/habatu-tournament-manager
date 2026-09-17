<?php

namespace Tests\Integration;

use App\Models\Category;
use App\Models\Section;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class TeamControllerTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------
    //  INDEX
    // ------------------------------------------------------------------

    public function test_index_returns_only_non_dummy_teams_with_relations(): void
    {
        $section  = Section::factory()->create();
        $category = Category::factory()->create();

        $visible = Team::factory()
            ->count(2)
            ->for($section)
            ->for($category)
            ->create();

        Team::factory()
            ->dummy()
            ->for($section)
            ->for($category)
            ->create();

        $response = $this->getJson('/api/teams');

        $response
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'section_id', 'category_id', 'section', 'category'],
            ]);

        // Verify that eager-loaded relations are present and correctly shaped.
        $first = $response->json(0);
        $this->assertEquals($section->id, $first['section']['id']);
        $this->assertEquals($category->id, $first['category']['id']);
    }

    // ------------------------------------------------------------------
    //  STORE
    // ------------------------------------------------------------------

    public function test_store_creates_team_and_returns_it(): void
    {
        $section  = Section::factory()->create();
        $category = Category::factory()->create();

        $payload = [
            'name'        => 'Pfadi Flamingo',
            'section_id'  => $section->id,
            'category_id' => $category->id,
        ];

        $response = $this->postJson('/api/teams', $payload);

        $response
            ->assertOk()
            ->assertJsonFragment(['name' => 'Pfadi Flamingo']);

        $this->assertDatabaseHas('teams', [
            'name'        => 'Pfadi Flamingo',
            'section_id'  => $section->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_store_rejects_missing_required_fields(): void
    {
        $response = $this->postJson('/api/teams', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'section_id', 'category_id']);
    }

    public function test_store_rejects_name_exceeding_max_length(): void
    {
        $section  = Section::factory()->create();
        $category = Category::factory()->create();

        $payload = [
            'name'        => str_repeat('A', 256),
            'section_id'  => $section->id,
            'category_id' => $category->id,
        ];

        $response = $this->postJson('/api/teams', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    // ------------------------------------------------------------------
    //  SHOW
    // ------------------------------------------------------------------

    public function test_show_returns_single_team(): void
    {
        $team = Team::factory()->create(['name' => 'Pfadi Dachs']);

        $response = $this->getJson("/api/teams/{$team->id}");

        $response
            ->assertOk()
            ->assertJsonFragment(['name' => 'Pfadi Dachs']);
    }

    public function test_show_returns_404_for_nonexistent_team(): void
    {
        $response = $this->getJson('/api/teams/99999');

        $response->assertNotFound();
    }

    // ------------------------------------------------------------------
    //  UPDATE
    // ------------------------------------------------------------------

    public function test_update_modifies_existing_team(): void
    {
        $team       = Team::factory()->create(['name' => 'Old Name']);
        $newSection = Section::factory()->create();

        $payload = [
            'name'       => 'New Name',
            'section_id' => $newSection->id,
        ];

        $response = $this->putJson("/api/teams/{$team->id}", $payload);

        $response
            ->assertOk()
            ->assertJsonFragment(['name' => 'New Name']);

        $this->assertDatabaseHas('teams', [
            'id'         => $team->id,
            'name'       => 'New Name',
            'section_id' => $newSection->id,
        ]);
    }

    public function test_update_returns_404_for_nonexistent_team(): void
    {
        $response = $this->putJson('/api/teams/99999', [
            'name'       => 'Ghost',
            'section_id' => 1,
        ]);

        $response->assertNotFound();
    }

    // ------------------------------------------------------------------
    //  DESTROY
    // ------------------------------------------------------------------

    public function test_destroy_deletes_team_and_returns_no_content(): void
    {
        $team = Team::factory()->create();

        $response = $this->deleteJson("/api/teams/{$team->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    public function test_destroy_returns_404_for_nonexistent_team(): void
    {
        $response = $this->deleteJson('/api/teams/99999');

        $response->assertNotFound();
    }
}
