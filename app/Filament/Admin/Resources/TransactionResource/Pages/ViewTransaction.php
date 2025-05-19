<?php

namespace App\Filament\Admin\Resources\TransactionResource\Pages;

use App\Filament\Admin\Resources\TransactionResource;
use Filament\Actions;
use App\Models\Transaction;
use Filament\Resources\Pages\ViewRecord;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;
    protected static string $view = 'filament.admin.resources.transaction-resource.pages.view-transaction';
    public ?Transaction $transaction = null;
    public function mount(int|string $record): void
    {
        parent::mount($record);
        $this->transaction = $this->getRecord();
    }
}
