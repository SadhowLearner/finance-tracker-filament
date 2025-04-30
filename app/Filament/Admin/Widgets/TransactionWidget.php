<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class TransactionWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $incomeTotal = Transaction::where('type', 'income')->sum('amount');

        $expenseTotal = Transaction::where('type', 'expense')->sum('amount');

        $balance = $incomeTotal - $expenseTotal;

        return [
            Stat::make('Total Income', number_format($incomeTotal, 0, ',', '.'))
                ->description('Pemasukan')
                ->descriptionIcon('heroicon-m-arrow-up-circle')
                ->color('success'),
            Stat::make('Total Expense', number_format($expenseTotal, 0, ',', '.'))
                ->description('Pengeluaran')
                ->descriptionIcon('heroicon-m-arrow-down-circle')
                ->color('danger'),
            Stat::make('Balance', number_format($balance, 0, ',', '.'))
                ->description('Saldo')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color($balance >= 0 ? 'success' : 'danger'),
        ];
    }
}
