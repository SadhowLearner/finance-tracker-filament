<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Source;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use App\Filament\Admin\Resources\SourceResource;
use App\Filament\Admin\Resources\CategoryResource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\TransactionResource\Pages;
use App\Filament\Admin\Resources\TransactionResource\RelationManagers;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')
                    ->required()
                    ->default(Auth::id()),
                DatePicker::make('date')
                    ->required(),
                TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('IDR')
                    ->afterStateUpdated(fn($state, $get, $set) => $set('total', $get('qty') * $state)),
                TextInput::make('qty')
                    ->default(1)
                    ->numeric()
                    ->minValue(1)
                    ->afterStateUpdated(fn($state, $get, $set) => $set('total', $get('price') * $state)),
                Select::make('type')
                    ->options([
                        'income' => 'Income',
                        'expense' => 'Expense',
                    ])
                    ->native(false)
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set) => $set('category_id', null)),
                Select::make('category_id')
                    ->label('Category')
                    ->options(
                        fn($get) =>
                        Category::query()
                            ->when($get('type'), fn($query, $type) => $query->where('type', $type))
                            ->pluck('name', 'id')
                    )
                    ->createOptionForm(
                        function (callable $get): array {
                            $newForm = CategoryResource::getForm();
                            $newForm[2] =
                                Hidden::make('type')
                                ->default($get('type'));

                            return $newForm;
                        }
                    )
                    ->createOptionUsing(function (array $data): Int {
                        return Category::create($data)->getKey();
                    })
                    ->required()
                    ->searchable()
                    ->placeholder('Select type first')
                    ->disabled(fn($get) => !$get('type'))
                    ->reactive(),
                Select::make('source_id')
                    ->label('Source')
                    ->options(
                        Source::pluck('name', 'id')
                    )
                    ->createOptionForm(SourceResource::getForm())
                    ->createOptionUsing(function (array $data): Int {
                        return Source::create($data)->getKey();
                    })
                    ->required()
                    ->searchable()
                    ->reactive(),
                TextInput::make('total')
                    ->default(fn($get) => $get('total'))
                    ->columnSpanFull()
                    ->prefix('Rp.'),
                // Select::make('source_id')
                //     ->relationship('source', 'name')
                //     ->required(),
                FileUpload::make('attachment')
                    ->directory('attachments')
                    ->disk('public')
                    ->columnSpanFull()
                    ->downloadable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('price')
                    ->sortable()
                    ->money('IDR'),
                TextColumn::make('qty')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable()
                    ->badge(),
                SelectColumn::make('category_id')
                    ->label('Category')
                    ->options(
                        fn($record) =>
                        Category::where('type', $record->type)->pluck('name', 'id')
                    )
                    ->searchable()
                    ->sortable(),
                TextColumn::make('source.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'income' => 'Income',
                        'expense' => 'Expense',
                    ]),
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
                SelectFilter::make('source')
                    ->relationship('source', 'name'),
                Tables\Filters\TernaryFilter::make('trashed')
                    ->nullable()
                    ->baseQuery(fn(Builder $query) => $query->withoutGlobalScopes([
                        SoftDeletingScope::class,
                    ]))
                    ->attribute('deleted_at')
                    ->label('Deleted')
                    ->placeholder('Not trashed transactions')
                    ->trueLabel('All transactions')
                    ->falseLabel('Trashed transactions')
                    ->queries(
                        true: fn(Builder $query) => $query,
                        false: fn(Builder $query) => $query->whereNotNull('deleted_at'),
                        blank: fn(Builder $query) => $query->whereNull('deleted_at'),
                    ),
                // Tables\Filters\TrashedFilter::make()
            ])
            ->actions([
                ForceDeleteAction::make(),
                RestoreAction::make(),
                EditAction::make(),
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
            'view' => Pages\ViewTransaction::route('/{record}/view'),
        ];
    }
}
