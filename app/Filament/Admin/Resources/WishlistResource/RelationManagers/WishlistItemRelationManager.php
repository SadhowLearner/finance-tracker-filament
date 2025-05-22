<?php

namespace App\Filament\Admin\Resources\WishlistResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Admin\Resources\CategoryResource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class WishlistItemRelationManager extends RelationManager
{
    protected static string $relationship = 'WishlistItem';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(4)
                    ->schema([
                        TextInput::make('name')->required()->columnSpanFull(),
                        TextInput::make('price')
                            ->numeric()
                            ->afterStateUpdated(fn($state, $get, $set) => $set('amount', $get('qty') * $state))
                            ->afterStateHydrated(fn($state, $get, $set) => $set('amount', $get('qty') * $state))
                            ->live(onBlur: true),
                        TextInput::make('qty')
                            ->default(1)
                            ->numeric()
                            ->minValue(1)
                            ->afterStateUpdated(fn($state, $get, $set) => $set('amount', $get('price') * $state))
                            ->afterStateHydrated(fn($state, $get, $set) => $set('amount', $get('price') * $state))
                            ->live(onBlur: true),
                        Select::make('category_id')
                            ->options(Category::where('type', 'expense')->pluck('name', 'id'))
                            ->searchable()
                            ->createOptionForm(
                                function (): array {
                                    $newForm = CategoryResource::getForm();
                                    $newForm[2] =
                                        TextInput::make('type')
                                        ->default('expense')
                                        ->readOnly()
                                        ->extraAttributes(['style' => 'text-transform: capitalize;']);

                                    return $newForm;
                                }
                            )
                            ->createOptionUsing(function (array $data): Int {
                                return Category::create($data)->getKey();
                            }),
                        Toggle::make('purchased')->inline(false),
                    ]),
                TextInput::make('amount')
                    ->disabled()
                    ->live(onBlur: true)
                    ->numeric()
                    ->prefix('Rp.')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('Name')->sortable()->searchable(),
                TextColumn::make('price')->label('Price')->sortable(),
                TextColumn::make('qty')->label('Quantity'),
                ToggleColumn::make('purchased'),
                TextColumn::make('total_price')
                    ->label('Total Harga')
                    ->getStateUsing(fn($record) => $record->qty * $record->price)
                    ->numeric()
                    ->prefix('Rp.'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
