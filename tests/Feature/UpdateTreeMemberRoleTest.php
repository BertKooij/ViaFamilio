<?php

namespace Tests\Feature;

use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTreeMemberRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_tree_member_roles_can_be_updated(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTree()->create());

        $user->currentTree->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $response = $this->put('/trees/'.$user->currentTree->id.'/members/'.$otherUser->id, [
            'role' => 'editor',
        ]);

        $this->assertTrue($otherUser->fresh()->hasTreeRole(
            $user->currentTree->fresh(), 'editor'
        ));
    }

    public function test_only_tree_owner_can_update_tree_member_roles(): void
    {
        $user = User::factory()->withPersonalTree()->create();

        $user->currentTree->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $this->actingAs($otherUser);

        $response = $this->put('/trees/'.$user->currentTree->id.'/members/'.$otherUser->id, [
            'role' => 'editor',
        ]);

        $this->assertTrue($otherUser->fresh()->hasTreeRole(
            $user->currentTree->fresh(), 'admin'
        ));
    }
}
