<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Category;
use App\Models\Wishlist;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Admin\Resources\CategoryResource;
use Filament\Tables\Actions\Action as TableAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\WishlistResource\Pages;
use App\Filament\Admin\Resources\WishlistResource\RelationManagers;
use App\Filament\Admin\Resources\WishlistResource\Pages\PrintWishlist;

class WishlistResource extends Resource
{
    protected static ?string $model = Wishlist::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')->required(),
                        Hidden::make('user_id')
                            ->required()
                            ->default(Auth::id()),
                        Textarea::make('description'),

                        Repeater::make('items')
                            ->relationship()
                            ->label('Wishlist Items')
                            ->schema([
                                Grid::make(3)
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
                                        TextInput::make('amount')
                                            ->disabled()
                                            ->live(onBlur: true)
                                            ->numeric()
                                            ->prefix('Rp.'),
                                    ]),
                            ])
                            ->afterStateUpdated(function ($state, $set) {
                                $total = array_reduce($state, fn($carry, $item) => $carry + $item['price'] * $item['qty'], 0);
                                $set('total', $total);
                            })
                            ->afterStateHydrated(function ($state, $set) {
                                $total = array_reduce($state, fn($carry, $item) => $carry + $item['price'] * $item['qty'], 0);
                            })
                            ->addActionLabel('Add Item')
                            ->collapsed(false)
                            ->cloneable()
                            ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
                            ->reorderable()
                            ->collapsible()
                            ->reactive()
                            ->defaultItems(1),

                        TextInput::make('total')
                            ->label('Total Amount')
                            ->default(fn($get) => $get('total') ?? 0)
                            ->columnSpanFull()
                            ->disabled()
                            ->reactive()
                            ->prefix('Rp.')
                            ->suffixActions([
                                Action::make('print')
                                    ->label('Print')
                                    ->icon('heroicon-o-printer')
                                    ->url(fn($record) => route('wishlist.print', $record))
                                    ->hidden(fn($record) => $record === null)
                                    ->openUrlInNewTab(),
                            ]),

                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => Str::limit($record->description, 30, '...', true))
                    ->limit(20),
                TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Total Items')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            // ->reorderable('sort')
            ->reorderable('sort', fn(): bool => Auth::check() && Auth::user()->isAdmin())
            ->paginatedWhileReordering()
            ->reorderRecordsTriggerAction(
                fn(TableAction $action, bool $isReordering) => $action
                    ->button()
                    ->label($isReordering ? 'Disable reordering' : 'Enable reordering'),
            );
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWishlists::route('/'),
            'create' => Pages\CreateWishlist::route('/create'),
            'edit' => Pages\EditWishlist::route('/{record}/edit'),
            // 'print' => Pages\PrintWishlist::route('/{record}/print'),
        ];
    }
}
