<?php

namespace Domain\Trees\Mails;

use Domain\Trees\Models\TreeInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class TreeInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public TreeInvitation $invitation;

    public function __construct(TreeInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.tree-invitation', ['acceptUrl' => URL::signedRoute('tree-invitations.accept', [
            'invitation' => $this->invitation,
        ])])->subject(__('Tree Invitation'));
    }
}
