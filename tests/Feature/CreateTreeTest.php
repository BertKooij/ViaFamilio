<?php

namespace Tests\Feature;

use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTreeTest extends TestCase
{
    use RefreshDatabase;

    public function test_trees_can_be_created(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTree()->create());

        $response = $this->post('/trees', [
            'name' => 'Test Tree',
        ]);

        $this->assertCount(2, $user->fresh()->ownedTrees);
        $this->assertEquals('Test Tree', $user->fresh()->ownedTrees()->latest('id')->first()->name);
    }
}
