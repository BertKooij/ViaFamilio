<?php

namespace Tests\Feature;

use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTreeNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_tree_names_can_be_updated(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTree()->create());

        $response = $this->put('/trees/'.$user->currentTree->id, [
            'name' => 'Test Tree',
        ]);

        $this->assertCount(1, $user->fresh()->ownedTrees);
        $this->assertEquals('Test Tree', $user->currentTree->fresh()->name);
    }
}
