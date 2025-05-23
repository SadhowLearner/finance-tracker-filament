<?php

namespace App\Filament\Exports;

use App\Models\Wishlist;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class WishlistExporter extends Exporter
{
    protected static ?string $model = Wishlist::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('user.name'),
            ExportColumn::make('name'),
            ExportColumn::make('description'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('type'),
            ExportColumn::make('sort'),
            ExportColumn::make('image'),
            ExportColumn::make('achieved'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your wishlist export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
