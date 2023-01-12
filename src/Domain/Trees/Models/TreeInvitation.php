<?php

namespace Domain\Trees\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreeInvitation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string<int, string>
     */
    protected $fillable = [
        'email',
        'role',
    ];

    /**
     * Get the tree that the invitation belongs to.
     */
    public function tree(): BelongsTo
    {
        return $this->belongsTo(Tree::class);
    }
}
