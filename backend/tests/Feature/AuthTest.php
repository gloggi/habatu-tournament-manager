<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_username_and_nickname(): void
    {
        $response = $this->postJson('/api/register', [
            'username' => 'testuser1',
            'nickname' => 'SameScoutName',
            'password' => 'secret123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['token', 'user']);

        $this->assertDatabaseHas('users', [
            'username' => 'testuser1',
            'nickname' => 'SameScoutName',
        ]);
    }

    public function test_different_users_can_have_the_same_nickname(): void
    {
        User::create([
            'username' => 'user_one',
            'nickname' => 'Tiger',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/register', [
            'username' => 'user_two',
            'nickname' => 'Tiger',
            'password' => 'password123',
        ]);

        $response->assertStatus(201);
        $this->assertEquals(2, User::where('nickname', 'Tiger')->count());
    }

    public function test_duplicate_username_is_rejected(): void
    {
        User::create([
            'username' => 'unique_user',
            'nickname' => 'NicknameOne',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/register', [
            'username' => 'unique_user',
            'nickname' => 'NicknameTwo',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username']);
    }

    public function test_user_can_login_with_username(): void
    {
        User::create([
            'username' => 'login_user',
            'nickname' => 'CoolNickname',
            'password' => Hash::make('mypassword'),
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'login_user',
            'password' => 'mypassword',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user']);
    }

    public function test_register_defaults_nickname_to_username_when_missing(): void
    {
        $response = $this->postJson('/api/register', [
            'username' => 'solouser',
            'password' => 'password123',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'username' => 'solouser',
            'nickname' => 'solouser',
        ]);
    }
}
