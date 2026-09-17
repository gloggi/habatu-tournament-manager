<?php

namespace Tests\Integration;

use App\Models\Hall;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HallControllerTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------
    //  INDEX
    // ------------------------------------------------------------------

    public function test_index_returns_all_halls(): void
    {
        $halls = Hall::factory()->count(3)->create();

        $response = $this->getJson('/api/halls');

        $response
            ->assertOk()
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'created_at', 'updated_at'],
            ]);

        $this->assertEquals(
            $halls->pluck('id')->sort()->values()->all(),
            collect($response->json())->pluck('id')->sort()->values()->all()
        );
    }

    // ------------------------------------------------------------------
    //  STORE
    // ------------------------------------------------------------------

    public function test_store_creates_hall_and_returns_it(): void
    {
        $payload = [
            'name' => 'Dreifachturnhalle 1',
        ];

        $response = $this->postJson('/api/halls', $payload);

        $response
            ->assertOk()
            ->assertJsonFragment(['name' => 'Dreifachturnhalle 1']);

        $this->assertDatabaseHas('halls', [
            'name' => 'Dreifachturnhalle 1',
        ]);
    }

    public function test_store_rejects_missing_name(): void
    {
        $response = $this->postJson('/api/halls', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_store_rejects_name_exceeding_max_length(): void
    {
        $payload = [
            'name' => str_repeat('A', 256),
        ];

        $response = $this->postJson('/api/halls', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    // ------------------------------------------------------------------
    //  SHOW
    // ------------------------------------------------------------------

    public function test_show_returns_single_hall(): void
    {
        $hall = Hall::factory()->create(['name' => 'Sporthalle Süd']);

        $response = $this->getJson("/api/halls/{$hall->id}");

        $response
            ->assertOk()
            ->assertJsonFragment(['name' => 'Sporthalle Süd']);
    }

    public function test_show_returns_404_for_nonexistent_hall(): void
    {
        $response = $this->getJson('/api/halls/99999');

        $response->assertNotFound();
    }

    // ------------------------------------------------------------------
    //  UPDATE
    // ------------------------------------------------------------------

    public function test_update_modifies_existing_hall(): void
    {
        $hall = Hall::factory()->create(['name' => 'Alte Halle']);

        $payload = [
            'name' => 'Renovierte Halle',
        ];

        $response = $this->putJson("/api/halls/{$hall->id}", $payload);

        $response
            ->assertOk()
            ->assertJsonFragment(['name' => 'Renovierte Halle']);

        $this->assertDatabaseHas('halls', [
            'id'   => $hall->id,
            'name' => 'Renovierte Halle',
        ]);
    }

    public function test_update_returns_404_for_nonexistent_hall(): void
    {
        $response = $this->putJson('/api/halls/99999', [
            'name' => 'Ghost Halle',
        ]);

        $response->assertNotFound();
    }

    // ------------------------------------------------------------------
    //  DESTROY
    // ------------------------------------------------------------------

    public function test_destroy_deletes_hall_and_returns_no_content(): void
    {
        $hall = Hall::factory()->create();

        $response = $this->deleteJson("/api/halls/{$hall->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('halls', ['id' => $hall->id]);
    }

    public function test_destroy_returns_404_for_nonexistent_hall(): void
    {
        $response = $this->deleteJson('/api/halls/99999');

        $response->assertNotFound();
    }
}
