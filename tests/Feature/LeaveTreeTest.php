<?php

namespace Tests\Feature;

use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveTreeTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_leave_trees(): void
    {
        $user = User::factory()->withPersonalTree()->create();

        $user->currentTree->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $this->actingAs($otherUser);

        $response = $this->delete('/trees/'.$user->currentTree->id.'/members/'.$otherUser->id);

        $this->assertCount(0, $user->currentTree->fresh()->users);
    }

    public function test_tree_owners_cant_leave_their_own_tree(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTree()->create());

        $response = $this->delete('/trees/'.$user->currentTree->id.'/members/'.$user->id);

        $response->assertSessionHasErrorsIn('removeTreeMember', ['tree']);

        $this->assertNotNull($user->currentTree->fresh());
    }
}
