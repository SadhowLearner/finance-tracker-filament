<x-filament-panels::page>
    <x-filament::section>
        <x-filament::card>
            <dl class="grid grid-cols-1 gap-3 text-sm">
                <div>
                    <dt class="font-semibold">ID</dt>
                    <dd>{{ $this->record->id }}</dd>
                </div>
                <div>
                    <dt class="font-semibold">Date</dt>
                    <dd>{{ \Carbon\Carbon::parse($this->record->date)->format('F j, Y') }}</dd>
                </div>
                <div>
                    <dt class="font-semibold">Description</dt>
                    <dd>{{ $this->record->description }}</dd>
                </div>
                <div>
                    <dt class="font-semibold">Amount</dt>
                    <dd class="{{ $this->record->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                        ${{ number_format($this->record->amount, 2) }}
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold">Type</dt>
                    <dd>{{ ucfirst($this->record->type) }}</dd>
                </div>
                <div>
                    <dt class="font-semibold">Category</dt>
                    <dd>{{ \App\Models\Category::find($this->record->category_id)->name }}</dd>
                </div>
                <div>
                    <dt class="font-semibold">Source</dt>
                    <dd>{{ \App\Models\Source::find($this->record->source_id)->name }}</dd>
                </div>
                <div>
                    <dt class="font-semibold">Attachment</dt>
                    <dd>{{ $this->record->attachment ?? 'None' }}</dd>
                </div>
                <div>
                    <dt class="font-semibold">Created At</dt>
                    <dd>{{ \Carbon\Carbon::parse($this->record->created_at)->toDayDateTimeString() }}</dd>
                </div>
                <div>
                    <dt class="font-semibold">Updated At</dt>
                    <dd>{{ \Carbon\Carbon::parse($this->record->updated_at)->toDayDateTimeString() }}</dd>
                </div>
            </dl>
        </x-filament::card>
    </x-filament::section>
</x-filament-panels::page>
