<?php

namespace Tests\Integration;

use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionControllerTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------
    //  INDEX
    // ------------------------------------------------------------------

    public function test_index_returns_all_sections(): void
    {
        $sections = Section::factory()->count(3)->create();

        $response = $this->getJson('/api/sections');

        $response
            ->assertOk()
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'created_at', 'updated_at'],
            ]);

        $this->assertEquals(
            $sections->pluck('id')->sort()->values()->all(),
            collect($response->json())->pluck('id')->sort()->values()->all()
        );
    }

    // ------------------------------------------------------------------
    //  STORE
    // ------------------------------------------------------------------

    public function test_store_creates_section_and_returns_it(): void
    {
        $payload = [
            'name' => 'Abteilung Wildsau',
        ];

        $response = $this->postJson('/api/sections', $payload);

        $response
            ->assertOk()
            ->assertJsonFragment(['name' => 'Abteilung Wildsau']);

        $this->assertDatabaseHas('sections', [
            'name' => 'Abteilung Wildsau',
        ]);
    }

    public function test_store_rejects_missing_name(): void
    {
        $response = $this->postJson('/api/sections', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_store_rejects_name_exceeding_max_length(): void
    {
        $payload = [
            'name' => str_repeat('A', 256),
        ];

        $response = $this->postJson('/api/sections', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    // ------------------------------------------------------------------
    //  SHOW
    // ------------------------------------------------------------------

    public function test_show_returns_single_section(): void
    {
        $section = Section::factory()->create(['name' => 'Abteilung Falkenstein']);

        $response = $this->getJson("/api/sections/{$section->id}");

        $response
            ->assertOk()
            ->assertJsonFragment(['name' => 'Abteilung Falkenstein']);
    }

    public function test_show_returns_404_for_nonexistent_section(): void
    {
        $response = $this->getJson('/api/sections/99999');

        $response->assertNotFound();
    }

    // ------------------------------------------------------------------
    //  UPDATE
    // ------------------------------------------------------------------

    public function test_update_modifies_existing_section(): void
    {
        $section = Section::factory()->create(['name' => 'Alte Abteilung']);

        $payload = [
            'name' => 'Neue Abteilung',
        ];

        $response = $this->putJson("/api/sections/{$section->id}", $payload);

        $response
            ->assertOk()
            ->assertJsonFragment(['name' => 'Neue Abteilung']);

        $this->assertDatabaseHas('sections', [
            'id'   => $section->id,
            'name' => 'Neue Abteilung',
        ]);
    }

    public function test_update_returns_404_for_nonexistent_section(): void
    {
        $response = $this->putJson('/api/sections/99999', [
            'name' => 'Ghost Abteilung',
        ]);

        $response->assertNotFound();
    }

    // ------------------------------------------------------------------
    //  DESTROY
    // ------------------------------------------------------------------

    public function test_destroy_deletes_section_and_returns_no_content(): void
    {
        $section = Section::factory()->create();

        $response = $this->deleteJson("/api/sections/{$section->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('sections', ['id' => $section->id]);
    }

    public function test_destroy_returns_404_for_nonexistent_section(): void
    {
        $response = $this->deleteJson('/api/sections/99999');

        $response->assertNotFound();
    }
}
