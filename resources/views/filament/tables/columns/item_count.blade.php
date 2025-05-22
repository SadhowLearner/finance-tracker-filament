<?php use App\Models\WishlistItem; ?>

@php
    $items = WishlistItem::where('wishlist_id', $getRecord()->id)->get();
    $count = $items->count();
@endphp

<div class="flex items-center gap-3">
    <div class="flex-shrink-0 w-1/4 text-center">
        <span class="text-4xl font-bold">
            {{ $count }}
        </span>
        <div class="text-sm text-gray-500">
            {{ Str::plural('item', $count) }}
        </div>
    </div>
    <div class="flex-1 ml-8">
        <ul class="list-none space-y-1 p-0 m-0 text-gray-500">
            @forelse($items as $item)
                <li>{{ $item->name }}</li>
            @empty
                <li class="text-red-400">No items</li>
            @endforelse
        </ul>
    </div>
</div>
