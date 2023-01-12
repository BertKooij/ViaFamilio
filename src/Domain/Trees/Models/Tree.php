<?php

namespace Domain\Trees\Models;

use Domain\Users\Models\Membership;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Jetstream;

class Tree extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'personal_tree' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string<int, string>
     */
    protected $fillable = [
        'name',
        'personal_tree',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    /**
     * Get the owner of the tree.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all of the tree's users including its owner.
     *
     * @return \Illuminate\Support\Collection
     */
    public function allUsers()
    {
        return $this->users->merge([$this->owner]);
    }

    /**
     * Get all of the users that belong to the tree.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, Membership::class)
            ->withPivot('role')
            ->withTimestamps()
            ->as('membership');
    }

    /**
     * Determine if the given user belongs to the tree.
     *
     * @param  \Domain\Users\Models\User  $user
     * @return bool
     */
    public function hasUser($user)
    {
        return $this->users->contains($user) || $user->ownsTree($this);
    }

    /**
     * Determine if the given email address belongs to a user on the tree.
     *
     * @param  string  $email
     * @return bool
     */
    public function hasUserWithEmail(string $email)
    {
        return $this->allUsers()->contains(function ($user) use ($email) {
            return $user->email === $email;
        });
    }

    /**
     * Determine if the given user has the given permission on the tree.
     *
     * @param  \Domain\Users\Models\User  $user
     * @param  string  $permission
     * @return bool
     */
    public function userHasPermission($user, $permission)
    {
        return $user->hasTreePermission($this, $permission);
    }

    /**
     * Get all of the pending user invitations for the tree.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function treeInvitations()
    {
        return $this->hasMany(TreeInvitation::class);
    }

    /**
     * Remove the given user from the tree.
     *
     * @param  \Domain\Users\Models\User $user
     * @return void
     */
    public function removeUser($user)
    {
        if ($user->current_tree_id === $this->id) {
            $user->forceFill([
                'current_tree_id' => null,
            ])->save();
        }

        $this->users()->detach($user);
    }

    /**
     * Purge all of the tree's resources.
     *
     * @return void
     */
    public function purge()
    {
        $this->owner()->where('current_tree_id', $this->id)
            ->update(['current_tree_id' => null]);

        $this->users()->where('current_tree_id', $this->id)
            ->update(['current_tree_id' => null]);

        $this->users()->detach();

        $this->delete();
    }
}
