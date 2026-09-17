<?php

namespace Tests\Integration;

use App\Models\Category;
use App\Models\Section;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------
    //  INDEX
    // ------------------------------------------------------------------

    public function test_index_returns_categories_with_non_dummy_teams_relation(): void
    {
        $section = Section::factory()->create();
        $category = Category::factory()->create(['name' => 'Kategorie 1']);

        // Create 2 normal teams and 1 dummy team in this category
        $visibleTeam1 = Team::factory()->for($section)->for($category)->create(['dummy' => false]);
        $visibleTeam2 = Team::factory()->for($section)->for($category)->create(['dummy' => false]);
        $dummyTeam = Team::factory()->dummy()->for($section)->for($category)->create();

        $response = $this->getJson('/api/categories');

        $response
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'color',
                    'teams' => [
                        '*' => ['id', 'name', 'dummy'],
                    ],
                ],
            ]);

        $teams = $response->json('0.teams');
        $this->assertCount(2, $teams);
        $teamIds = collect($teams)->pluck('id')->all();
        $this->assertContains($visibleTeam1->id, $teamIds);
        $this->assertContains($visibleTeam2->id, $teamIds);
        $this->assertNotContains($dummyTeam->id, $teamIds);
    }

    // ------------------------------------------------------------------
    //  STORE
    // ------------------------------------------------------------------

    public function test_store_creates_category_and_returns_it(): void
    {
        $payload = [
            'name'  => 'Kategorie A',
            'color' => '#FF5733',
        ];

        $response = $this->postJson('/api/categories', $payload);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name'  => 'Kategorie A',
                'color' => '#FF5733',
            ]);

        $this->assertDatabaseHas('categories', [
            'name'  => 'Kategorie A',
            'color' => '#FF5733',
        ]);
    }

    public function test_store_rejects_missing_required_fields(): void
    {
        $response = $this->postJson('/api/categories', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'color']);
    }

    public function test_store_rejects_fields_exceeding_max_length(): void
    {
        $payload = [
            'name'  => str_repeat('A', 256),
            'color' => str_repeat('B', 256),
        ];

        $response = $this->postJson('/api/categories', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'color']);
    }

    // ------------------------------------------------------------------
    //  SHOW
    // ------------------------------------------------------------------

    public function test_show_returns_single_category(): void
    {
        $category = Category::factory()->create([
            'name'  => 'Kategorie B',
            'color' => '#00FF00',
        ]);

        $response = $this->getJson("/api/categories/{$category->id}");

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name'  => 'Kategorie B',
                'color' => '#00FF00',
            ]);
    }

    public function test_show_returns_404_for_nonexistent_category(): void
    {
        $response = $this->getJson('/api/categories/99999');

        $response->assertNotFound();
    }

    // ------------------------------------------------------------------
    //  UPDATE
    // ------------------------------------------------------------------

    public function test_update_modifies_existing_category_with_partial_fields(): void
    {
        $category = Category::factory()->create([
            'name'  => 'Alte Kategorie',
            'color' => '#000000',
        ]);

        // Partial update: only updating the name
        $response = $this->putJson("/api/categories/{$category->id}", [
            'name' => 'Neue Kategorie',
        ]);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name'  => 'Neue Kategorie',
                'color' => '#000000',
            ]);

        $this->assertDatabaseHas('categories', [
            'id'    => $category->id,
            'name'  => 'Neue Kategorie',
            'color' => '#000000',
        ]);
    }

    public function test_update_returns_404_for_nonexistent_category(): void
    {
        $response = $this->putJson('/api/categories/99999', [
            'name' => 'Ghost Category',
        ]);

        $response->assertNotFound();
    }

    // ------------------------------------------------------------------
    //  DESTROY
    // ------------------------------------------------------------------

    public function test_destroy_deletes_category_and_returns_no_content(): void
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_destroy_returns_404_for_nonexistent_category(): void
    {
        $response = $this->deleteJson('/api/categories/99999');

        $response->assertNotFound();
    }
}
