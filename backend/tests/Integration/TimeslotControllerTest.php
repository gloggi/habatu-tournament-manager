<?php

namespace Tests\Integration;

use App\Models\Timeslot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeslotControllerTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------
    //  INDEX
    // ------------------------------------------------------------------

    public function test_index_returns_all_timeslots(): void
    {
        $timeslots = Timeslot::factory()->count(3)->sequence(
            ['start_time' => '09:00', 'end_time' => '09:20'],
            ['start_time' => '09:20', 'end_time' => '09:40'],
            ['start_time' => '09:40', 'end_time' => '10:00'],
        )->create();

        $response = $this->getJson('/api/timeslots');

        $response
            ->assertOk()
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'start_time', 'end_time', 'temporary', 'created_at', 'updated_at'],
            ]);

        $this->assertEquals(
            $timeslots->pluck('id')->sort()->values()->all(),
            collect($response->json())->pluck('id')->sort()->values()->all()
        );
    }

    // ------------------------------------------------------------------
    //  STORE
    // ------------------------------------------------------------------

    public function test_store_creates_timeslot_and_returns_it(): void
    {
        $payload = [
            'start_time' => '14:00',
            'end_time'   => '14:20',
        ];

        $response = $this->postJson('/api/timeslots', $payload);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'start_time' => '14:00',
                'end_time'   => '14:20',
            ]);

        $this->assertDatabaseHas('timeslots', [
            'start_time' => '14:00',
            'end_time'   => '14:20',
        ]);
    }

    public function test_store_rejects_missing_required_fields(): void
    {
        $response = $this->postJson('/api/timeslots', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['start_time', 'end_time']);
    }

    public function test_store_rejects_fields_exceeding_max_length(): void
    {
        $payload = [
            'start_time' => str_repeat('1', 256),
            'end_time'   => str_repeat('2', 256),
        ];

        $response = $this->postJson('/api/timeslots', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['start_time', 'end_time']);
    }

    // ------------------------------------------------------------------
    //  SHOW
    // ------------------------------------------------------------------

    public function test_show_returns_single_timeslot(): void
    {
        $timeslot = Timeslot::factory()->create([
            'start_time' => '11:00',
            'end_time'   => '11:20',
        ]);

        $response = $this->getJson("/api/timeslots/{$timeslot->id}");

        $response
            ->assertOk()
            ->assertJsonFragment([
                'start_time' => '11:00',
                'end_time'   => '11:20',
            ]);
    }

    public function test_show_returns_404_for_nonexistent_timeslot(): void
    {
        $response = $this->getJson('/api/timeslots/99999');

        $response->assertNotFound();
    }

    // ------------------------------------------------------------------
    //  UPDATE
    // ------------------------------------------------------------------

    public function test_update_modifies_existing_timeslot(): void
    {
        $timeslot = Timeslot::factory()->create([
            'start_time' => '10:00',
            'end_time'   => '10:20',
        ]);

        $payload = [
            'start_time' => '10:30',
            'end_time'   => '10:50',
        ];

        $response = $this->putJson("/api/timeslots/{$timeslot->id}", $payload);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'start_time' => '10:30',
                'end_time'   => '10:50',
            ]);

        $this->assertDatabaseHas('timeslots', [
            'id'         => $timeslot->id,
            'start_time' => '10:30',
            'end_time'   => '10:50',
        ]);
    }

    public function test_update_rejects_missing_required_fields(): void
    {
        $timeslot = Timeslot::factory()->create();

        $response = $this->putJson("/api/timeslots/{$timeslot->id}", []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['start_time', 'end_time']);
    }

    public function test_update_returns_404_for_nonexistent_timeslot(): void
    {
        $response = $this->putJson('/api/timeslots/99999', [
            'start_time' => '10:00',
            'end_time'   => '10:20',
        ]);

        $response->assertNotFound();
    }

    // ------------------------------------------------------------------
    //  DESTROY
    // ------------------------------------------------------------------

    public function test_destroy_deletes_timeslot_and_returns_no_content(): void
    {
        $timeslot = Timeslot::factory()->create();

        $response = $this->deleteJson("/api/timeslots/{$timeslot->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('timeslots', ['id' => $timeslot->id]);
    }

    public function test_destroy_returns_404_for_nonexistent_timeslot(): void
    {
        $response = $this->deleteJson('/api/timeslots/99999');

        $response->assertNotFound();
    }
}
