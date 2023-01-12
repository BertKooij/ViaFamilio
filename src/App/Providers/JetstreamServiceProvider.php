<?php

namespace App\Providers;

use App\Middleware\ShareUserData;
use Domain\Trees\Actions\AddTreeMember;
use Domain\Trees\Actions\CreateTree;
use Domain\Trees\Actions\DeleteTree;
use Domain\Trees\Actions\InviteTreeMember;
use Domain\Trees\Actions\RemoveTreeMember;
use Domain\Trees\Actions\UpdateTreeName;
use Domain\Trees\Models\Tree;
use Domain\Trees\Models\TreeInvitation;
use Domain\Users\Actions\DeleteUser;
use Domain\Users\Models\Membership;
use Domain\Users\Models\User;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Jetstream::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();

        Jetstream::createTeamsUsing(CreateTree::class);
        Jetstream::updateTeamNamesUsing(UpdateTreeName::class);
        Jetstream::addTeamMembersUsing(AddTreeMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTreeMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTreeMember::class);
        Jetstream::deleteTeamsUsing(DeleteTree::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);

        Jetstream::useTeamModel(Tree::class);
        Jetstream::useUserModel(User::class);
        Jetstream::useTeamInvitationModel(TreeInvitation::class);
        Jetstream::useMembershipModel(Membership::class);

        $kernel = $this->app->make(Kernel::class);
        $kernel->appendMiddlewareToGroup('web', ShareUserData::class);
        $kernel->appendToMiddlewarePriority(ShareUserData::class);
    }

    /**
     * Configure the roles and permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::role('admin', 'Administrator', [
            'create',
            'read',
            'update',
            'delete',
        ])->description('Administrator users can perform any action.');

        Jetstream::role('editor', 'Editor', [
            'read',
            'create',
            'update',
        ])->description('Editor users have the ability to read, create, and update.');
    }
}
