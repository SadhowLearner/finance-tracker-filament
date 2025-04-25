<?php

namespace App\Filament\Admin\Resources\SourceResource\Pages;

use App\Filament\Admin\Resources\SourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSource extends EditRecord
{
    protected static string $resource = SourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
