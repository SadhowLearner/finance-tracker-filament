<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WishlistItem extends Model
{
    protected $guarded = ['id', 'timestamps'];

    /**
     * Get the wishlist that owns the WishlistItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wishlist(): BelongsTo
    {
        return $this->belongsTo(Wishlist::class);
    }
}
