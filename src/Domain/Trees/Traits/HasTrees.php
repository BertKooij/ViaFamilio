<?php

namespace Domain\Trees\Traits;

use Domain\Trees\Models\Tree;
use Illuminate\Support\Str;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\OwnerRole;
use Laravel\Sanctum\HasApiTokens;

trait HasTrees
{
    /**
     * Determine if the given tree is the current tree.
     *
     * @param  mixed  $tree
     * @return bool
     */
    public function isCurrentTree($tree)
    {
        return $tree->id === $this->currentTree->id;
    }

    /**
     * Get the current tree of the user's context.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currentTree()
    {
        if (is_null($this->current_tree_id) && $this->id) {
            $this->switchTree($this->personalTree());
        }

        return $this->belongsTo(Tree::class, 'current_tree_id');
    }

    /**
     * Switch the user's context to the given tree.
     *
     * @param  mixed  $tree
     * @return bool
     */
    public function switchTree($tree)
    {
        if (! $this->belongsToTree($tree)) {
            return false;
        }

        $this->forceFill([
            'current_tree_id' => $tree->id,
        ])->save();

        $this->setRelation('currentTree', $tree);

        return true;
    }

    /**
     * Get all of the trees the user owns or belongs to.
     *
     * @return \Illuminate\Support\Collection
     */
    public function allTrees()
    {
        return $this->ownedTrees->merge($this->trees)->sortBy('name');
    }

    /**
     * Get all of the trees the user owns.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ownedTrees()
    {
        return $this->hasMany(Tree::class);
    }

    /**
     * Get all of the trees the user belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function trees()
    {
        return $this->belongsToMany(Tree::class, Jetstream::membershipModel())
            ->withPivot('role')
            ->withTimestamps()
            ->as('membership');
    }

    /**
     * Get the user's "personal" tree.
     *
     * @return \App\Models\Tree
     */
    public function personalTree()
    {
        return $this->ownedTrees->where('personal_tree', true)->first();
    }

    /**
     * Determine if the user owns the given tree.
     *
     * @param  mixed  $tree
     * @return bool
     */
    public function ownsTree($tree)
    {
        if (is_null($tree)) {
            return false;
        }

        return $this->id == $tree->{$this->getForeignKey()};
    }

    /**
     * Determine if the user belongs to the given tree.
     *
     * @param  mixed  $tree
     * @return bool
     */
    public function belongsToTree($tree)
    {
        if (is_null($tree)) {
            return false;
        }

        return $this->ownsTree($tree) || $this->trees->contains(function ($t) use ($tree) {
                return $t->id === $tree->id;
            });
    }

    /**
     * Get the role that the user has on the tree.
     *
     * @param  mixed  $tree
     * @return \Laravel\Jetstream\Role|null
     */
    public function treeRole($tree)
    {
        if ($this->ownsTree($tree)) {
            return new OwnerRole;
        }

        if (! $this->belongsToTree($tree)) {
            return;
        }

        $role = $tree->users
            ->where('id', $this->id)
            ->first()
            ->membership
            ->role;

        return $role ? Jetstream::findRole($role) : null;
    }

    /**
     * Determine if the user has the given role on the given tree.
     *
     * @param  mixed  $tree
     * @param  string  $role
     * @return bool
     */
    public function hasTreeRole($tree, string $role)
    {
        if ($this->ownsTree($tree)) {
            return true;
        }

        return $this->belongsToTree($tree) && optional(Jetstream::findRole($tree->users->where(
                'id', $this->id
            )->first()->membership->role))->key === $role;
    }

    /**
     * Get the user's permissions for the given tree.
     *
     * @param  mixed  $tree
     * @return array
     */
    public function treePermissions($tree)
    {
        if ($this->ownsTree($tree)) {
            return ['*'];
        }

        if (! $this->belongsToTree($tree)) {
            return [];
        }

        return (array) optional($this->treeRole($tree))->permissions;
    }

    /**
     * Determine if the user has the given permission on the given tree.
     *
     * @param  mixed  $tree
     * @param  string  $permission
     * @return bool
     */
    public function hasTreePermission($tree, string $permission)
    {
        if ($this->ownsTree($tree)) {
            return true;
        }

        if (! $this->belongsToTree($tree)) {
            return false;
        }

        if (in_array(HasApiTokens::class, class_uses_recursive($this)) &&
            ! $this->tokenCan($permission) &&
            $this->currentAccessToken() !== null) {
            return false;
        }

        $permissions = $this->treePermissions($tree);

        return in_array($permission, $permissions) ||
            in_array('*', $permissions) ||
            (Str::endsWith($permission, ':create') && in_array('*:create', $permissions)) ||
            (Str::endsWith($permission, ':update') && in_array('*:update', $permissions));
    }
}
