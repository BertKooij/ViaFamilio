<?php

namespace Tests\Feature;

use Domain\Trees\Models\Tree;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTreeTest extends TestCase
{
    use RefreshDatabase;

    public function test_trees_can_be_deleted(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTree()->create());

        $user->ownedTrees()->save($tree = Tree::factory()->make([
            'personal_tree' => false,
        ]));

        $tree->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'test-role']
        );

        $response = $this->delete('/trees/'.$tree->id);

        $this->assertNull($tree->fresh());
        $this->assertCount(0, $otherUser->fresh()->trees);
    }

    public function test_personal_trees_cant_be_deleted(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTree()->create());

        $response = $this->delete('/trees/'.$user->currentTree->id);

        $this->assertNotNull($user->currentTree->fresh());
    }
}
