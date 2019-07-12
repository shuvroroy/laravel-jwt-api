<?php

namespace Tests\Feature\User\Setting;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_requires_a_unique_email()
    {
        $user = factory(User::class)->create([
            'email' => 'john@example.com'
        ]);

        $data = [
            'email' => $user['email'],
        ];

        $this->actingAs($this->user)
            ->json('PATCH', route('profile.update'), $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'email'
                ]
            ]);
    }

    /** @test */
    public function it_can_update_profile_info()
    {
        $this->actingAs($this->user)
            ->json('PATCH', route('profile.update'), [
                'name' => $name = 'John Doe',
                'email' => $email = 'john@example.com',
            ])
            ->assertStatus(202)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email'
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => $name,
            'email' => $email,
        ]);
    }
}
