<?php

namespace Tests\Feature;

use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemoveTreeMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_tree_members_can_be_removed_from_trees(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTree()->create());

        $user->currentTree->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $response = $this->delete('/trees/'.$user->currentTree->id.'/members/'.$otherUser->id);

        $this->assertCount(0, $user->currentTree->fresh()->users);
    }

    public function test_only_tree_owner_can_remove_tree_members(): void
    {
        $user = User::factory()->withPersonalTree()->create();

        $user->currentTree->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $this->actingAs($otherUser);

        $response = $this->delete('/trees/'.$user->currentTree->id.'/members/'.$user->id);

        $response->assertStatus(403);
    }
}
