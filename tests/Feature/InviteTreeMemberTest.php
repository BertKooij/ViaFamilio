<?php

namespace Tests\Feature;

use Domain\Trees\Mails\TreeInvitationMail;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InviteTreeMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_tree_members_can_be_invited_to_tree(): void
    {
        Mail::fake();

        $this->actingAs($user = User::factory()->withPersonalTree()->create());

        $this->post('/trees/'.$user->currentTree->id.'/members', [
            'email' => 'test@example.com',
            'role' => 'admin',
        ]);

        Mail::assertSent(TreeInvitationMail::class);

        $this->assertCount(1, $user->currentTree->fresh()->treeInvitations);
    }

    public function test_tree_member_invitations_can_be_cancelled(): void
    {
        Mail::fake();

        $this->actingAs($user = User::factory()->withPersonalTree()->create());

        $invitation = $user->currentTree->treeInvitations()->create([
            'email' => 'test@example.com',
            'role' => 'admin',
        ]);

        $response = $this->delete('/tree-invitations/'.$invitation->id);

        $this->assertCount(0, $user->currentTree->fresh()->treeInvitations);
    }
}
