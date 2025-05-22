<?php

namespace App\Filament\Admin\Resources\WishlistResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables\Columns\ToggleColumn;
use App\Filament\Admin\Resources\WishlistResource;
use App\Filament\Admin\Resources\WishlistResource\RelationManagers\WishlistItemRelationManager;

class ViewWishlistItem extends ViewRecord
{
    protected static string $resource = WishlistResource::class;

    public function getRelationManagers(): array
    {
        return [
            WishlistItemRelationManager::class,
        ];
    }
}
