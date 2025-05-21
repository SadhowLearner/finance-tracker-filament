<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionStatWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Transactions', Transaction::count())
                ->description('Jumlah Transaksi')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
            Stat::make('Last Transaction', optional(Transaction::orderByDesc('date')->first())->price * Transaction::orderByDesc('date')->first()->qty
                ? number_format(optional(Transaction::orderByDesc('date')->first())->price * Transaction::orderByDesc('date')->first()->qty, 0, ',', '.')
                : 'You doesnt have a Transaction')
                ->description(optional(Transaction::orderByDesc('date')->first())->date)
                ->descriptionIcon(optional(Transaction::orderByDesc('date')->first())->type == 'income' ? 'heroicon-m-arrow-up-circle' : 'heroicon-m-arrow-down-circle')
                ->color(optional(Transaction::orderByDesc('date')->first())->type == 'income' ? 'success' : 'danger'),
        ];
    }
    protected function getColumns(): int
    {
        return 2;
    }
}
