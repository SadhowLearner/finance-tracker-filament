<div class="p-6 bg-white shadow rounded-lg space-y-4">
  <h2 class="text-2xl font-bold text-gray-800">Wishlist: {{ $record->name }}</h2>

  <div><strong>Description:</strong> {{ $record->description ?? 'N/A' }}</div>
  <div><strong>Status:</strong> {{ $record->achieved ? 'Achieved' : 'Not achieved' }}</div>
  <div><strong>Type:</strong> {{ ucfirst($record->type) }}</div>
  <div><strong>Sort Order:</strong> {{ $record->sort }}</div>
  <div><strong>Created At:</strong> {{ $record->created_at->toFormattedDateString() }}</div>

  <div class="pt-4">
    <h3 class="text-lg font-semibold">Items</h3>
    <ul class="list-disc list-inside">
      @forelse($record->items as $item)
        <li>{{ $item->name }} {{$item->purchased ? '(purchased)' : ''}}</li>
      @empty
        <li class="text-red-500">No items</li>
      @endforelse
    </ul>
  </div>
</div>