<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class TransactionWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $incomeTotal = Transaction::where('type', 'income')->sum('amount');

        $expenseTotal = Transaction::where('type', 'expense')->sum('amount');

        $balance = $incomeTotal - $expenseTotal;

        return [
            Stat::make('Total Income', number_format($incomeTotal, 0, ',', '.'))
                ->description('Pendapatan Masuk')
                ->descriptionIcon('heroicon-m-arrow-up-circle')
                ->color('succes'),
            Stat::make('Total Expense', number_format($expenseTotal, 0, ',', '.'))
                ->description('Pengeluaran')
                ->descriptionIcon('heroicon-m-arrow-down-circle')
                ->color('danger'),
            Stat::make('Balance', number_format($balance, 0, ',', '.'))
                ->description('Saldo')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color($balance >= 0 ? 'success' : 'danger'),
            Stat::make('Total Transactions', Transaction::count())
                ->description('Jumlah Transaksi')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
            Stat::make('Last Transaction', Transaction::latest()->first()->created_at->diffForHumans() || 'You doesnt have a Transaction')
        ];
    }
}
